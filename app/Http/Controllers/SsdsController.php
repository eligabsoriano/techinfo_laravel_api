<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SsdsController extends Controller
{
    public function index()
    {
       return Ssds::all();
    }

    public function store(Request $request)
    {
        $fields = $request->validate([
            'ssd_name'       => 'required|string',
            'brand'          => 'required|string',
            'interface_type' => 'required|string',
            'capacity_gb'    => 'required|integer',
        ]);

        $ssds = Ssds::create($fields);

        return response()->json(['message'=> 'Created successful'],200);
    }

    public function show($id)
    {
        $ssds = Ssds::findOrFail($id);
        return response()->json([
            'status' => true,
            'message' => 'Ssd data found successfully',
            'data' => $ssds
        ], 200);
    }



public function update(Request $request, $ssds)
{
    $fields = $request->validate([
            'ssd_name'       => 'required|string',
            'brand'          => 'required|string',
            'interface_type' => 'required|string',
            'capacity_gb'    => 'required|integer',
    ]);

    $ssds = Ssds::find($ssds);

    $ssds->update($fields);

    return response()->json($ssds, 201);
}



    public function destroy($ssds)
    {
        $ssds = Ssds::find($ssds);
        $ssds->delete();

        return response()->json([
            'message' => 'Ssd data deleted successfully'
        ], 200);
    }
}
