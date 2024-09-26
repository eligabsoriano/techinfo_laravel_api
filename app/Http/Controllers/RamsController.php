<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class RamsController extends Controller
{
    public function index()
    {
       return Rams::all();
    }

    public function store(Request $request)
    {
        $fields = $request->validate([
            'ram_name'         => 'required|string',
            'brand'            => 'required|string',
            'ram_type'         => 'required|string',
            'ram_capacity_gb'  => 'required|integer',
            'ram_speed_mhz'    => 'required|integer',

        ]);

        $rams = Rams::create($fields);

        return response()->json(['message'=> 'Created successful'],200);
    }

    public function show($id)
    {
        $rams = Rams::findOrFail($id);
        return response()->json([
            'status' => true,
            'message' => 'Ram data found successfully',
            'data' => $rams
        ], 200);
    }


public function update(Request $request, $rams)
{
    $fields = $request->validate([
            'ram_name'         => 'required|string',
            'brand'            => 'required|string',
            'ram_type'         => 'required|string',
            'ram_capacity_gb'  => 'required|integer',
            'ram_speed_mhz'    => 'required|integer',
    ]);

    $rams = Rams::find($rams);

    $rams->update($fields);

    return response()->json($rams, 201);
}

    public function destroy($rams)
    {
        $rams = Rams::find($rams);
        $rams->delete();

        return response()->json([
            'message' => 'Ram data deleted successfully'
        ], 200);
    }
}
