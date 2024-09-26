<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CpuCoolersController extends Controller
{
    public function index()
    {
       return CpuCoolers::all();
    }

    public function store(Request $request)
    {
        $fields = $request->validate([
            'cooler_name'          => 'required|string',
            'brand'                => 'required|string',
            'socket_type_supported' => 'required|string',
            'max_cooler_height_mm'  => 'required|integer',
        ]);

        $cpu_coolers = CpuCoolers::create($fields);

        return response()->json(['message'=> 'Created successful'],200);
    }

    public function show($id)
    {
        $cpu_coolers = CpuCoolers::findOrFail($id);
        return response()->json([
            'status' => true,
            'message' => 'Cpu Cooler data found successfully',
            'data' => $cpu_coolers
        ], 200);
    }



public function update(Request $request, $cpu_coolers)
{
    $fields = $request->validate([
        'cooler_name'          => 'required|string',
        'brand'                => 'required|string',
        'socket_type_supported' => 'required|string',
        'max_cooler_height_mm'  => 'required|integer',
    ]);

    $cpu_coolers = CpuCoolers::find($cpu_coolers);

    $cpu_coolers->update($fields);

    return response()->json($cpu_coolers, 201);
}



    public function destroy($cpu_coolers)
    {
        $cpu_coolers = CpuCoolers::find($cpu_coolers);
        $cpu_coolers->delete();

        return response()->json([
            'message' => 'Cpu Cooler data deleted successfully'
        ], 200);
    }
}
