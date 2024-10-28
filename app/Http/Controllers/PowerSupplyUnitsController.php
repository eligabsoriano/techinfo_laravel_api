<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PowerSupplyUnits;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class PowerSupplyUnitsController extends Controller
{
    // Get request
    public function index()
    {
        return PowerSupplyUnits::all()->map(function($power_supply_units) {
            $power_supply_units->performance_score = $this->calculatePsuPerformance($power_supply_units);
            return $power_supply_units;
        });
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
            'gpu_6_pin_connectors'   => 'required|string', //
            'gpu_8_pin_connectors'   => 'required|string', //
            'gpu_12_pin_connectors'  => 'nullable|string',  // High-end GPUs may need 12-pin connectors
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

        $performanceScore = $this->calculatePsuPerformance($power_supply_units);

        return response()->json([
            'status' => true,
            'message' => 'Power Supply Unit data found successfully',
            'data' => $power_supply_units,
            'performance_score' => $performanceScore
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
        'gpu_6_pin_connectors'   => 'required|string', //
        'gpu_8_pin_connectors'   => 'required|string', //
        'gpu_12_pin_connectors'  => 'nullable|string',  // High-end GPUs may need 12-pin connectors
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
    private function calculatePsuPerformance($power_supply_units): float
    {
        // Step 1: Get the highest wattage from the database and convert it to a float
        $database_max_wattage = (float) DB::table('power_supply_units')
            ->select(DB::raw('MAX(CAST(SUBSTRING(wattage, 1, LENGTH(wattage) - 1) AS DECIMAL(10,2))) AS max_wattage'))
            ->value('max_wattage');

        // Debugging: Log the maximum wattage
        \Log::info('Database Max Wattage: ' . $database_max_wattage);

        // Step 2: Sanitize and extract the current wattage of the power supply unit
        $wattage = (float) filter_var(trim($power_supply_units->wattage), FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION); // e.g., "750W"

        // Step 3: Calculate the performance score as a ratio between the PSU's wattage and the highest wattage
        $performanceScore = ($wattage / $database_max_wattage) * 100;

        // Step 4: Define a maximum score threshold (optional)
        $maxPerformanceScore = 100; // Normalized to 100%

        // Step 5: Normalize the score and ensure it doesn't exceed 100%
        return round(min($performanceScore, $maxPerformanceScore)); // Return the percentage score
    }

}
