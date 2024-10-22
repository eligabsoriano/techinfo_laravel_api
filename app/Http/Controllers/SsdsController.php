<?php

namespace App\Http\Controllers;

use App\Models\Ssds;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class SsdsController extends Controller
{
    // Get request
    public function index()
    {
        return Ssds::all()->map(function($ssds) {
            $ssds->performance_score = $this->calculateSsdPerformance($ssds);
            return $ssds;
        });
    }

    // Post for creating
    public function store(Request $request)
    {
        $fields = Validator::make($request->all(), [
            'ssd_name'       => 'required|string',
            'brand'          => 'required|string',
            'interface_type' => 'required|string',
            'capacity_gb'    => 'required|string|regex:/^\d+\s*GB$/', // Ensure GB format
            'link'           => 'required|string'
        ]);

        if ($fields->fails()) {
            return response()->json([
                'message' => 'All fields are mandatory',
                'error'   => $fields->errors()
            ], 422);
        }

        $validatedData = $fields->validated();
        $ssds = Ssds::create($validatedData);

        return response()->json([
            'status' => true,
            'message' => 'Created successfully',
            'data' => $ssds
        ], 200);
    }

    // Get request by specific ID
    public function show($ssd_id)
    {
        $ssds = Ssds::find($ssd_id);

        if (!$ssds) {
            return response()->json([
                'status' => false,
                'message' => 'SSD data not found'
            ], 404);
        }

        $performanceScore = $this->calculateSsdPerformance($ssds);

        return response()->json([
            'status' => true,
            'message' => 'SSD data found successfully',
            'data' => $ssds,
            'performance_score' => $performanceScore
        ], 200);
    }

    // Update
    public function update(Request $request, $ssd_id)
    {
        $fields = Validator::make($request->all(), [
            'ssd_name'       => 'required|string',
            'brand'          => 'required|string',
            'interface_type' => 'required|string',
            'capacity_gb'    => 'required|string|regex:/^\d+\s*GB$/', // Ensure GB format
            'link'           => 'required|string'
        ]);

        if ($fields->fails()) {
            return response()->json([
                'message' => 'All fields are mandatory',
                'error'   => $fields->errors()
            ], 422);
        }

        $ssds = Ssds::find($ssd_id);

        if (!$ssds) {
            return response()->json([
                'status' => false,
                'message' => 'SSD data not found'
            ], 404);
        }

        $ssds->update($fields->validated());

        return response()->json([
            'status' => true,
            'message' => 'SSD Data Updated Successfully',
            'data' => $ssds
        ], 201);
    }

    // Delete requests by specific ID
    public function destroy($ssd_id)
    {
        $ssds = Ssds::find($ssd_id);
        if (!$ssds) {
            return response()->json([
                'status' => false,
                'message' => 'SSD data not found'
            ], 404);
        }

        $ssds->delete();

        return response()->json([
            'message' => 'SSD data deleted successfully',
            'data' => $ssds
        ], 200);
    }

    private function calculateSsdPerformance($ssds): float
    {
        // Step 1: Get the highest SSD capacity from the database and convert it to a float
        $database_max_capacity = (float) DB::table('ssds')
            ->select(DB::raw('MAX(CAST(SUBSTRING(capacity_gb, 1, LENGTH(capacity_gb) - 2) AS DECIMAL(10,2))) AS max_capacity'))
            ->value('max_capacity');

        // Debugging: Log the maximum SSD capacity
        \Log::info('Database Max SSD Capacity: ' . $database_max_capacity);

        // Step 2: Sanitize and extract the current SSD capacity, ensuring correct conversion
        $capacity = (float) filter_var(trim($ssds->capacity_gb), FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
        // Ensure to remove "GB" if it's there
        $capacity = (float) str_replace(['GB', ' '], '', $ssds->capacity_gb);

        // Step 3: Calculate the performance score as a ratio between the SSD's capacity and the highest capacity
        if ($database_max_capacity > 0) {
            $performanceScore = ($capacity / $database_max_capacity) * 100;
        } else {
            $performanceScore = 0; // Handle the case where the max capacity is zero
        }

        // Step 4: Define a maximum score threshold if needed (optional)
        $maxPerformanceScore = 100; // Normalized to 100%

        // Step 5: Normalize the score and ensure it doesn't exceed 100%
        return round(min($performanceScore, $maxPerformanceScore)); // Return the percentage score
    }

}
