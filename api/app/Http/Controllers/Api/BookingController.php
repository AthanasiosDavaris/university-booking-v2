<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Reservation\CheckAvailabilityRequest;
use App\Http\Requests\Reservation\StoreReservationRequest;
use App\Http\Requests\Reservation\UpdateReservationStatusRequest;
use App\Models\Room;
use App\Models\Reservation;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class BookingController extends Controller
{
    /**
     * GET /api/rooms/availability
     * Uses temporal overlap algebra: (starts_at < requested_end) AND (ends_at > requested_start)
     */
    public function availability(CheckAvailabilityRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $query = Room::query()->with('building');

        // Physical constraints filter
        $query->when(isset($validated['capacity']), fn($q) => $q->where('capacity', '>=', $validated['capacity']))
              ->when(isset($validated['exam_capacity']), fn($q) => $q->where('exam_capacity', '>=', $validated['exam_capacity']))
              ->when(isset($validated['room_type']), fn($q) => $q->where('type', $validated['room_type']))
              ->when(isset($validated['is_accessible']), fn($q) => $q->where('is_wheelchair_accessible', $validated['is_accessible']))
              ->when(isset($validated['has_projector']), fn($q) => $q->where('has_projector', $validated['has_projector']))
              ->when(isset($validated['has_ac']), fn($q) => $q->where('has_ac', $validated['has_ac']));

        // Exclude rooms with overlapping confirmed or pending reservations
        $query->whereDoesntHave('reservations', function ($q) use ($validated) {
            $q->whereIn('status', ['confirmed', 'pending'])
              ->where('starts_at', '<', $validated['ends_at'])
              ->where('ends_at', '>', $validated['starts_at']);
        });

        return response()->json($query->get());
    }

    /**
     * POST /api/reservations
     * Supports: pessimistic | optimistic | none (for thesis benchmarks)
     */
    public function store(StoreReservationRequest $request): JsonResponse
    {
        $strategy  = $request->header('X-Lock-Strategy', 'pessimistic');
        $validated = $request->validated();
        $user      = $request->user();

        // Admins, secretaries and professors default to confirmed; students default to pending
        $initialStatus = $validated['status'] ?? (in_array($user->role?->name, ['admin', 'secretary', 'professor']) ? 'confirmed' : 'pending');

        try {
            $reservation = match ($strategy) {
                'pessimistic' => $this->storePessimistic($validated, $user->id, $initialStatus),
                'optimistic'  => $this->storeOptimistic($validated, $user->id, $initialStatus),
                'none'        => $this->storeNone($validated, $user->id, $initialStatus),
                default       => abort(400, 'Invalid X-Lock-Strategy header.'),
            };

            return response()->json([
                'message'       => 'Reservation created successfully.',
                'strategy_used' => $strategy,
                'reservation'   => $reservation
            ], 201);

        } catch (QueryException $e) {
            $sqlState = $e->errorInfo[0] ?? $e->getCode();
            
            // SQLSTATE 23P01: exclusion_violation
            if ($sqlState === '23P01' || $e->getCode() === '23P01') {
                return response()->json([
                    'error'         => 'Database Constraint Violation',
                    'message'       => 'Race condition intercepted by PostgreSQL GiST exclusion constraint.',
                    'strategy_used' => $strategy
                ], 409);
            }
            
            return response()->json([
                'error'   => 'Database Error',
                'message' => 'Concurrent write conflict occurred.'
            ], 409);
        }
    }

    /**
     * PATCH /api/reservations/{reservation}/status
     */
    public function updateStatus(UpdateReservationStatusRequest $request, Reservation $reservation): JsonResponse
    {
        $validated = $request->validated();

        $reservation->update([
            'status'           => $validated['status'],
            'rejection_reason' => $validated['rejection_reason'] ?? null,
            'reviewed_by_id'   => $request->user()->id,
            'reviewed_at'      => now(),
        ]);

        return response()->json([
            'message'     => 'Reservation status updated successfully.',
            'reservation' => $reservation
        ]);
    }

    /**
     * SERIALIZED: Row-level lock via SELECT ... FOR UPDATE
     */
    private function storePessimistic(array $data, int $userId, string $status): Reservation
    {
        return DB::transaction(function () use ($data, $userId, $status) {
            // Exclusive row lock on the room
            $room = Room::where('id', $data['room_id'])->lockForUpdate()->firstOrFail();

            if ($this->hasOverlap($room->id, $data['starts_at'], $data['ends_at'])) {
                abort(409, 'The room is already booked for the requested time slot.');
            }

            return $this->createReservationRecord($data, $userId, $status);
        });
    }

    /**
     * NON-SERIALIZED: Check and insert without DB lock
     */
    private function storeOptimistic(array $data, int $userId, string $status): Reservation
    {
        if ($this->hasOverlap($data['room_id'], $data['starts_at'], $data['ends_at'])) {
            abort(409, 'The room is already booked for the requested time slot.');
        }

        return $this->createReservationRecord($data, $userId, $status);
    }

    /**
     * BLIND INSERT: Relies directly on the PostgreSQL GiST exclusion constraint
     */
    private function storeNone(array $data, int $userId, string $status): Reservation
    {
        return $this->createReservationRecord($data, $userId, $status);
    }

    /**
     * Helper to persist reservation with purpose support
     */
    private function createReservationRecord(array $data, int $userId, string $status): Reservation
    {
        return Reservation::create([
            'room_id'   => $data['room_id'],
            'user_id'   => $userId,
            'title'     => $data['title'],
            'purpose'   => $data['purpose'] ?? 'lecture',
            'starts_at' => $data['starts_at'],
            'ends_at'   => $data['ends_at'],
            'status'    => $status,
        ]);
    }

    /**
     * Helper for temporal overlap check
     */
    private function hasOverlap(int $roomId, string $startsAt, string $endsAt): bool
    {
        return Reservation::where('room_id', $roomId)
            ->whereIn('status', ['confirmed', 'pending'])
            ->where('starts_at', '<', $endsAt)
            ->where('ends_at', '>', $startsAt)
            ->exists();
    }
}