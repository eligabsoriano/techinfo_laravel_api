<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ProcessorsController extends Controller
{
    public function index()
    {
       return Processors::all();
    }

    public function store(Request $request)
    {
        $fields = $request->validate([
            'processor_name'    => 'required|string',
            'brand'             => 'required|string',
            'socket_type'       => 'required|string',
            'tdp_wattage'       => 'required|integer',
            'base_clock_speed'  => 'required|numeric',
            'max_clock_speed'   => 'required|numeric',

        ]);

        $processors = Processors::create($fields);

        return response()->json(['message'=> 'Created successful'],200);
    }

    public function show($id)
    {
        $processors = Processors::findOrFail($id);
        return response()->json([
            'status' => true,
            'message' => 'Processors data found successfully',
            'data' => $processors
        ], 200);
    }



public function update(Request $request, $processors)
{
    $fields = $request->validate([
            'processor_name'    => 'required|string',
            'brand'             => 'required|string',
            'socket_type'       => 'required|string',
            'tdp_wattage'       => 'required|integer',
            'base_clock_speed'  => 'required|numeric',
            'max_clock_speed'   => 'required|numeric',
    ]);

    $processors = Processors::find($processors);

    $processors->update($fields);

    return response()->json($processors, 201);
}



    public function destroy($processors)
    {
        $processors = Processors::find($processors);
        $processors->delete();

        return response()->json([
            'message' => 'Processors data deleted successfully'
        ], 200);
    }
}
