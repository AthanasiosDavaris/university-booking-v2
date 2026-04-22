<?php

namespace App\Http\Controllers;

use App\Models\Reservations;
use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReservationController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'room_id' => 'required|exists:rooms,id',
            'title' => 'required|string|max:255',
            'day_of_week' => 'required|integer|between:0,6',
            'start_at' => 'required|date_format:H:i',
            'end_at' => 'required|date_format:H:i|after:start_at',
            'from' => 'required|date',
            'to' => 'required|date|after_or_equal:from',
            'comment' => 'nullable|string',
        ]);

        // Execute within a database transaction
        $reservation = DB::transaction(function () use ($validated, $request) {
            
            // PESSIMISTIC LOCKING: Lock the specific room row
            // Any concurrent request trying to book this room will pause here until this transaction completes
            $room = Room::where('id', $validated['room_id'])->lockForUpdate()->firstOrFail();

            // Check for overlaps
            $overlap = Reservation::where('room_id', $room->id)
                ->whereIn('status', ['pending', 'confirmed'])
                ->where('day_of_week', $validated['day_of_week'])
                // Date overlap: (Existing.from <= New.to) AND (Existing.to >= New.from)
                ->where('from', '<=', $validated['to'])
                ->where('to', '>=', $validated['from'])
                // Time overlap: (Existing.start_at < New.end_at) AND (Existing.end_at > New.start_at)
                ->where('start_at', '<', $validated['end_at'])
                ->where('end_at', '>', $validated['start_at'])
                ->exists();
            
                if ($overlap) {
                    // Abort throws an HttpException which Laravel automatically formats as a JSON 409 Conflict response
                    abort(409, 'The room is already booked for the requested time slot.');
                }

                // Create the reservation
                return Reservation::create([
                    'title' => $validated['title'],
                    'room_id' => $room->id,
                    'user_id' => $request->user()->id,
                    'day_of_week' => $validated['day_of_week'],
                    'start_at' => $validated['start_at'],
                    'end_at' => $validated['end_at'],
                    'from' => $validated['from'],
                    'to' => $validated['to'],
                    'comment' => $validated['comment'] ?? null,
                    'status' => 'pending', // Default status
                ]);
        });

        return response()->json([
            'message' => 'Reservation created successfully.',
            'reservation' => $reservation
        ], 201);
    }
}
