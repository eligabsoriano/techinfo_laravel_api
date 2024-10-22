<?php

namespace App\Http\Controllers;

use App\Models\Hdds;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class HddsController extends Controller
{
    // Get request
    public function index()
    {
        return Hdds::all()->map(function($hdds) {
            $hdds->performance_score = $this->calculateHddPerformance($hdds);
            return $hdds;
        });
    }

    // Post for creating
    public function store(Request $request)
    {
        $fields = Validator::make($request->all(), [
            'hdd_name'       => 'required|string',
            'brand'          => 'required|string',
            'interface_type' => 'required|string',
            'capacity_gb'    => 'required|string',
            'link'                  => 'required|string'
        ]);

        if($fields->fails()){
            return response()->json([
                'message'=>'All fields are mandatory',
                'error'=>$fields->errors()
            ], 422);
        }

        $validatedData = $fields->validated();
        $hdds = Hdds::create($validatedData);

        return response()->json([
            'status'=> true,
            'message'=> 'Created successful',
            'data' => $hdds
        ], 200);
    }

    // Get request by specific ID
    public function show($hdd_id)
    {
        $hdds = Hdds::find($hdd_id);
        if (!$hdds) {
            return response()->json([
                'status' => false,
                'message' => 'HDD data not found'
            ], 404);
        }

        $performanceScore = $this->calculateHddPerformance($hdds);

        return response()->json([
            'status' => true,
            'message' => 'HDD data found successfully',
            'data' => $hdds,
            'performance_score' => $performanceScore
        ], 200);
    }

    //Update
public function update(Request $request, $hdds)
{
    $fields = Validator::make($request->all(), [
        'hdd_name'       => 'required|string',
        'brand'          => 'required|string',
        'interface_type' => 'required|string',
        'capacity_gb'    => 'required|string',
        'link'                  => 'required|string'
    ]);

    if($fields->fails()){
        return response()->json([
            'message'=>'All fields are mandatory',
            'error'=>$fields->errors()
        ], 422);
    }

    $hdds = Hdds::find($hdds);
    if (!$hdds) {
        return response()->json([
            'status' => false,
            'message' => 'HDD data not found'
        ], 404);
    }

    $hdds->update($fields->validated());

    return response()->json([
        'status' => true,
        'message' => 'HDD Data Updated Successfully',
        'data' => $hdds], 201);
}

    // Delete requests by specific ID
    public function destroy($hdds)
    {
        $hdds = Hdds::find($hdds);
        if (!$hdds) {
            return response()->json([
                'status' => false,
                'message' => 'HDD data not found'
            ], 404);
        }
        $hdds->delete();

        return response()->json([
            'message' => 'Hdd data deleted successfully',
            'data' => $hdds
        ], 200);
    }

    private function calculateHddPerformance($hdds): float
    {
        // Step 1: Get the highest HDD capacity from the database and convert it to a float
        $database_max_capacity = (float) DB::table('hdds')
            ->select(DB::raw('MAX(CAST(SUBSTRING(capacity_gb, 1, LENGTH(capacity_gb) - 2) AS DECIMAL(10,2))) AS max_capacity'))
            ->value('max_capacity');

        // Debugging: Log the maximum HDD capacity
        \Log::info('Database Max HDD Capacity: ' . $database_max_capacity);

        // Step 2: Sanitize and extract relevant data for the current HDD
        $capacity = (float) filter_var(trim($hdds->capacity_gb), FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);


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
