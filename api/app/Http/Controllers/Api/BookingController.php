<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CheckAvailabilityRequest;
use App\Http\Requests\StoreReservationRequest;
use App\Http\Requests\UpdateReservationStatusRequest;
use App\Models\Room;
use App\Models\Reservation;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class BookingController extends Controller
{
    /**
     * GET /api/rooms/availability
     * Uses Allen's Interval Algebra to detect temporal overlaps.
     */
    public function availability(CheckAvailabilityRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $query = Room::query();

        // Apply physical constraints dynamically
        $query->when($request->filled('capacity'), fn($q) => $q->where('capacity', '>=', $validated['capacity']))
              ->when($request->filled('exam_capacity'), fn($q) => $q->where('exam_capacity', '>=', $validated['exam_capacity']))
              ->when($request->filled('room_type'), fn($q) => $q->where('type', $validated['room_type']))
              ->when($request->filled('is_accessible'), fn($q) => $q->where('is_accessible', $validated['is_accessible']))
              ->when($request->filled('has_hvac'), fn($q) => $q->where('has_hvac', $validated['has_hvac']));

        //  Apply temporal constraints (Exclude rooms with overlapping bookings)
        //  Logic: (ExistingStart < NewEnd) AND (ExistingEnd > NewStart)
        $query->whereDoesntHave('reservations', function ($q) use ($validated) {
            $q->whereIn('status', ['approved', 'pending'])
              ->where('start_time', '<', $validated['end_time'])
              ->where('end_time', '>', $validated['start_time']);
        });

        return response()->json($query->get());
    }

    /**
     * POST /api/reservations
     * Implements dynamic concurrency control for thesis benchmarking.
     */
    public function store(StoreReservationRequest $request): JsonResponse
    {
        $strategy = $request->header('X-Lock-Strategy', 'pessimistic');
        $validated = $request->validated();
        $userId = $request->user()->id;

        try {
            // Route to the appropriate locking strategy
            $reservation = match ($strategy) {
                'pessimistic' => $this->storePessimistic($validated, $userId),
                'optimistic'  => $this->storeOptimistic($validated, $userId),
                'none'        => $this->storeNone($validated, $userId),
                default       => abort(400, 'Invalid X-Lock-Strategy header.'),
            };

            return response()->json($reservation, 201);
        } catch (QueryException $e) {
            // SQLSTATE 23P01: exclusion_violation
            if ($e->getCode() === '23P01') {
                return response()->json([
                    'error' => 'Database Constraint Violation',
                    'message' => 'Race condition intercepted by PostgreSQL GiST exclusion constraint.',
                    'strategy_used' => $strategy
                ], 409);
            }
            throw $e;
        }
    }

    /**
     * PATCH /api/reservations/{reservation}/status
     */
    public function updateStatus(UpdateReservationStatusRequest $request, Reservation $reservation): JsonResponse
    {
        $reservation->update($request->validated());

        return response()->json($reservation);
    }

    /**
     * SERIALIZED: Locks the parent Room row. Concurrent requests for the SAME room 
     * will queue at the database level until this transaction commits/rolls back.
     */
    private function storePessimistic(array $data, string $userId): Reservation
    {
        return DB::transaction(function () use ($data, $userId) {
            $room = Room::where('id', $data['room_id'])->lockForUpdate()->firstOrFail();

            if ($this->hasOverlap($room->id, $data['start_time'], $data['end_time'])) {
                abort(409, 'Room is already booked for the requested time slot.');
            }

            return Reservation::create([...$data, 'user_id' => $userId, 'status' => 'pending']);
        });
    }

    /**
     * NON-SERIALIZED: Checks app-level state, then attempts insert. 
     * Highly susceptible to race conditions under load.
     */
    private function storeOptimistic(array $data, string $userId): Reservation
    {
        if ($this->hasOverlap($data['room_id'], $data['start_time'], $data['end_time'])) {
            abort(409, 'Room is already booked for the requested time slot.');
        }

        return Reservation::create([...$data, 'user_id' => $userId, 'status' => 'pending']);
    }

    /**
     * BLIND INSERT: Relies 100% on the PostgreSQL GiST constraint.
     */
    private function storeNone(array $data, string $userId): Reservation
    {
        return Reservation::create([...$data, 'user_id' => $userId, 'status' => 'pending']);
    }

    /**
     * Helper for temporal overlap check
     */
    private function hasOverlap(string $roomId, string $startTime, string $endTime): bool
    {
        return Reservation::where('room_id', $roomId)
            ->whereIn('status', ['approved', 'pending'])
            ->where('start_time', '<', $endTime)
            ->where('end_time', '>', $startTime)
            ->exists();
    }
}