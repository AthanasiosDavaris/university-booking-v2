<?php

namespace App\Http\Controllers;

use App\Models\Building;
use Illuminate\Http\Request;

class BuildingController extends Controller
{
    public function index()
    {
        return response()->json(Building::all());
    }

    public function show(Building $building)
    {
        return response()->json($building);
    }
}
