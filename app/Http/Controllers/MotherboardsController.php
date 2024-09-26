<?php

namespace App\Http\Controllers;

use App\Models\Motherboards;
use Illuminate\Http\Request;

class MotherboardsController extends Controller
{
    public function index()
    {
       return Motherboards::all();
    }

    public function store(Request $request)
    {
        $fields = $request->validate([
            'motherboard_name' => 'required|string',
            'brand'            => 'required|string',
            'socket_type'      => 'required|string',
            'ram_type'         => 'required|string',
            'max_ram_slots'    => 'required|integer',
            'gpu_interface'     => 'required|string',
            'form_factor'      => 'required|string',
        ]);

        $motherboards = Motherboards::create($fields);

        return response()->json(['message'=> 'Created successful'],200);
    }

    public function show($id)
    {
        $motherboards = Motherboards::findOrFail($id);
        return response()->json([
            'status' => true,
            'message' => 'Motherboard data found successfully',
            'data' => $motherboards
        ], 200);
    }



public function update(Request $request, $motherboards)
{
    $fields = $request->validate([
        'motherboard_name' => 'required|string',
        'brand'            => 'required|string',
        'socket_type'      => 'required|string',
        'ram_type'         => 'required|string',
        'max_ram_slots'    => 'required|integer',
        'gpu_interface'     => 'required|string',
        'form_factor'      => 'required|string',
    ]);

    $motherboards = Motherboards::find($motherboards);

    $motherboards->update($fields);

    return response()->json($motherboards, 201);
}



    public function destroy($motherboards)
    {
        $motherboards = Motherboards::find($motherboards);
        $motherboards->delete();

        return response()->json([
            'message' => 'Motherboard data deleted successfully'
        ], 200);
    }
}
