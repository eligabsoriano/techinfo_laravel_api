<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CompatibilitiesController extends Controller
{
    public function index()
    {
       return Compatibilities::all();
    }

    public function store(Request $request)
    {
        $fields = $request->validate([
            'processor_id'   => 'required|exists:processors,id',
            'motherboard_id' => 'required|exists:motherboards,id',
            'ram_id'         => 'required|exists:ram,id',
            'gpu_id'         => 'required|exists:gpus,id',
            'psu_id'         => 'required|exists:power_supply_units,id',
            'case_id'        => 'required|exists:computer_cases,id',
            'cooler_id'      => 'required|exists:cpu_coolers,id',
            'hdd_id'         => 'nullable|exists:hdds,id',
            'ssd_id'         => 'nullable|exists:ssds,id',

        ]);

        $compatibilities = Compatibilities::create($fields);

        return response()->json(['message'=> 'Created successful'],200);
    }

    public function show($id)
    {
        $compatibilities = Compatibilities::findOrFail($id);
        return response()->json([
            'status' => true,
            'message' => 'Compatibility data found successfully',
            'data' => $compatibilities
        ], 200);
    }



public function update(Request $request, $compatibilities)
{
    $fields = $request->validate([
            'processor_id'   => 'required|exists:processors,id',
            'motherboard_id' => 'required|exists:motherboards,id',
            'ram_id'         => 'required|exists:rams,id',
            'gpu_id'         => 'required|exists:gpus,id',
            'psu_id'         => 'required|exists:power_supply_units,id',
            'case_id'        => 'required|exists:computer_cases,id',
            'cooler_id'      => 'required|exists:cpu_coolers,id',
            'hdd_id'         => 'nullable|exists:hdds,id',
            'ssd_id'         => 'nullable|exists:ssds,id',
    ]);

    $compatibilities = Compatibilites::find($compatibilities);

    $compatibilities->update($fields);

    return response()->json($compatibilities, 201);
}



    public function destroy($compatibilities)
    {
        $compatibilities = Compatibilities::find($compatibilities);
        $compatibilities->delete();

        return response()->json([
            'message' => 'Compatibility data deleted successfully'
        ], 200);
    }

}
