<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class GpusController extends Controller
{
    public function index()
    {
       return Gpus::all();
    }

    public function store(Request $request)
    {
        $fields = $request->validate([
            'gpu_name'         => 'required|string',
            'brand'            => 'required|string',
            'interface_type'   => 'required|string',
            'tdp_wattage'      => 'required|integer',
            'gpu_length_mm'    => 'required|integer',

        ]);

        $gpus = Gpus::create($fields);

        return response()->json(['message'=> 'Created successful'],200);
    }

    public function show($id)
    {
        $gpus = Gpus::findOrFail($id);
        return response()->json([
            'status' => true,
            'message' => 'Gpu data found successfully',
            'data' => $gpus
        ], 200);
    }



public function update(Request $request, $gpus)
{
    $fields = $request->validate([
            'gpu_name'         => 'required|string',
            'brand'            => 'required|string',
            'interface_type'   => 'required|string',
            'tdp_wattage'      => 'required|integer',
            'gpu_length_mm'    => 'required|integer',

    ]);

    $gpus = Gpus::find($gpus);

    $gpus->update($fields);

    return response()->json($gpus, 201);
}



    public function destroy($gpus)
    {
        $gpus = Gpus::find($gpus);
        $gpus->delete();

        return response()->json([
            'message' => 'Gpu data deleted successfully'
        ], 200);
    }
}
