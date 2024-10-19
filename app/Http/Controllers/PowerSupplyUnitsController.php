<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PowerSupplyUnits;
use Illuminate\Support\Facades\Validator;

class PowerSupplyUnitsController extends Controller
{
    // Get request
    public function index()
    {
       return PowerSupplyUnits::all();
    }

    // Post for creating
    public function store(Request $request)
    {
        $fields = Validator::make($request->all(), [
            'psu_name'               => 'required|string',
            'brand'                  => 'required|string',
            'wattage'                => 'required|string|regex:/^\d+\s*W$/i',
            'continuous_wattage'     => 'required|string|regex:/^\d+\s*W$/i',  // Use this for validation
            'efficiency_rating'      => 'required|string',
            'has_required_connectors'=> 'required|boolean', // May be calculated based on connectors below
            'gpu_6_pin_connectors'   => 'required|integer', //
            'gpu_8_pin_connectors'   => 'required|integer', //
            'gpu_12_pin_connectors'  => 'nullable|integer',  // High-end GPUs may need 12-pin connectors
            'link'                  => 'required|string'
        ]);

        if($fields->fails()){
            return response()->json([
                'message'=>'All fields are mandatory',
                'error'=>$fields->errors()
            ], 422);
        }

        $validatedData = $fields->validated();
        $power_supply_units = PowerSupplyUnits::create($validatedData);

        return response()->json([
            'status'=> true,
            'message'=> 'Created successful',
            'data' => $power_supply_units
        ], 200);
    }

    // Get request by specific ID
    public function show($psu_id)
    {
        $power_supply_units = PowerSupplyUnits::find($psu_id);
        if (!$power_supply_units) {
            return response()->json([
                'status' => false,
                'message' => 'Power Supply Unit data not found'
            ], 404);
        }
        return response()->json([
            'status' => true,
            'message' => 'Power Supply Unit data found successfully',
            'data' => $power_supply_units
        ], 200);
    }

    // Update
public function update(Request $request, $power_supply_units)
{
    $fields = Validator::make($request->all(), [
        'psu_name'               => 'required|string',
        'brand'                  => 'required|string',
        'wattage'                => 'required|string|regex:/^\d+\s*W$/i',
        'continuous_wattage'     => 'required|string|regex:/^\d+\s*W$/i',  // Use this for validation
        'efficiency_rating'      => 'required|string',
        'has_required_connectors'=> 'required|boolean', // May be calculated based on connectors below
        'gpu_6_pin_connectors'   => 'required|integer', //
        'gpu_8_pin_connectors'   => 'required|integer', //
        'gpu_12_pin_connectors'  => 'nullable|integer',  // High-end GPUs may need 12-pin connectors
        'link'                  => 'required|string'
    ]);

    if($fields->fails()){
        return response()->json([
            'message'=>'All fields are mandatory',
            'error'=>$fields->errors()
        ], 422);
    }

    $power_supply_units = PowerSupplyUnits::find($power_supply_units);
    if (!$power_supply_units) {
        return response()->json([
            'status' => false,
            'message' => 'Power Supply Unit data not found'
        ], 404);
    }

    $power_supply_units->update($fields->validated());

    return response()->json([
        'status' => true,
        'message' => 'Power Supply Unit Data Updated Successfully',
        'data' => $power_supply_units], 201);

    }

    // Delete requests by specific ID
    public function destroy($power_supply_units)
    {
        $power_supply_units = PowerSupplyUnits::find($power_supply_units);
        if (!$power_supply_units) {
            return response()->json([
                'status' => false,
                'message' => 'Power Supply Unit data not found'
            ], 404);
        }
        $power_supply_units->delete();

        return response()->json([
            'message' => 'Power Supply Unit data deleted successfully',
            'data' => $power_supply_units
        ], 200);
    }
}
