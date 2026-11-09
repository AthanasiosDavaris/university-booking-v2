<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Room;
use Illuminate\Http\Request;

class RoomController extends Controller
{
    public function index()
    {
        // Eager load the building relationship
        return response()->json(Room::with('building')->get());
    }

    public function show(Room $room)
    {
        // Load the building relationship for the single room
        return response()->json($room->load('building'));
    }
}
