<?php

namespace App\Http\Controllers;

use App\Models\Lane;
use Illuminate\Http\Request;

class LaneController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $lanes = Lane::with(['tickets' => function ($query) {
            $query->orderBy('position', 'asc');
        }])->get();
        return response()->json(['success' => true, 'data' => $lanes, 'message' => 'Lanes retrieved successfully'], 200);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}