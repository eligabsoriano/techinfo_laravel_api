<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Compatibilities;
use Illuminate\Support\Facades\Validator;

class CompatibilitiesController extends Controller
{
     // Get request
    public function index()
    {
       return Compatibilities::all();
    }

    // Post for creating
    public function store(Request $request)
    {
        $fields = Validator::make($request->all(), [
            'processor_id'   => 'required|exists:processors,processor_id',
            'motherboard_id' => 'required|exists:motherboards,motherboard_id',
            'ram_id'         => 'required|exists:rams,ram_id',
            'gpu_id'         => 'required|exists:gpuses,gpu_id',
            'psu_id'         => 'required|exists:power_supply_units,psu_id',
            'case_id'        => 'required|exists:computer_cases,case_id',
            'cooler_id'      => 'required|exists:cpu_coolers,cooler_id',
            'hdd_id'         => 'nullable|exists:hdds,hdd_id',
            'ssd_id'         => 'nullable|exists:ssds,ssd_id',
        ]);

        if($fields->fails()){
            return response()->json([
                'message'=>'All fields are mandatory',
                'error'=>$fields->errors()
            ], 422);
        }

        $validatedData = $fields->validated();
        $compatibilities = Compatibilities::create($validatedData);

        return response()->json([
            'status'=> true,
            'message'=> 'Created successful',
            'data' => $compatibilities
        ], 200);
    }

    // Get request by specific ID
    public function show($compatibility_id)
    {
        $compatibilities = Compatibilities::find($compatibility_id);
        if (!$compatibilities) {
            return response()->json([
                'status' => false,
                'message' => 'Compatibility data not found'
            ], 404);
        }
        return response()->json([
            'status' => true,
            'message' => 'Compatibility data found successfully',
            'data' => $compatibilities
        ], 200);
    }
    //Update
public function update(Request $request, $compatibilities)
{
    $fields = Validator::make($request->all(), [
        'processor_id'   => 'required|exists:processors,processor_id',
        'motherboard_id' => 'required|exists:motherboards,motherboard_id',
        'ram_id'         => 'required|exists:rams,ram_id',
        'gpu_id'         => 'required|exists:gpuses,gpu_id',
        'psu_id'         => 'required|exists:power_supply_units,psu_id',
        'case_id'        => 'required|exists:computer_cases,case_id',
        'cooler_id'      => 'required|exists:cpu_coolers,cooler_id',
        'hdd_id'         => 'nullable|exists:hdds,hdd_id',
        'ssd_id'         => 'nullable|exists:ssds,ssd_id',
    ]);

    if($fields->fails()){
        return response()->json([
            'message'=>'All fields are mandatory',
            'error'=>$fields->errors()
        ], 422);
    }

    $compatibilities = Compatibilites::find($compatibilities);
    if (!$compatibilities) {
        return response()->json([
            'status' => false,
            'message' => 'Compatibility data not found'
        ], 404);
    }

    $compatibilities->update($fields->validated());
    return response()->json([
        'status' => true,
        'message' => 'Compatibility Data Updated Successfully',
        'data' => $compatibilities], 201);
}
    // Delete requests by specific ID
    public function destroy($compatibilities)
    {
        $compatibilities = Compatibilities::find($compatibilities);
        if (!$compatibilities) {
            return response()->json([
                'status' => false,
                'message' => 'Compatibility data not found'
            ], 404);
        }
        $compatibilities->delete();

        return response()->json([
            'message' => 'Compatibility data deleted successfully'
        ], 200);
    }

}
