<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HddsController extends Controller
{
    public function index()
    {
       return Hdds::all();
    }

    public function store(Request $request)
    {
        $fields = $request->validate([
            'hdd_name'      => 'required|string',
            'brand'         => 'required|string',
            'interface_type' => 'required|string',
            'capacity_gb'   => 'required|integer',
        ]);

        $hdds = Hdds::create($fields);

        return response()->json(['message'=> 'Created successful'],200);
    }

    public function show($id)
    {
        $hdds = Hdds::findOrFail($id);
        return response()->json([
            'status' => true,
            'message' => 'Hdd data found successfully',
            'data' => $hdds
        ], 200);
    }



public function update(Request $request, $hdds)
{
    $fields = $request->validate([
            'hdd_name'      => 'required|string',
            'brand'         => 'required|string',
            'interface_type' => 'required|string',
            'capacity_gb'   => 'required|integer',
    ]);

    $hdds = Hdds::find($hdds);

    $hdds->update($fields);

    return response()->json($hdds, 201);
}



    public function destroy($hdds)
    {
        $hdds = Hdds::find($hdds);
        $hdds->delete();

        return response()->json([
            'message' => 'Hdd data deleted successfully'
        ], 200);
    }
}
