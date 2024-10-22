<?php

namespace App\Http\Controllers;

use App\Models\Rams;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class RamsController extends Controller
{
    // Get request
    public function index()
    {
        return Rams::all()->map(function($rams) {
            $rams->performance_score = $this->calculateRamPerformance($rams);
            return $rams;
        });
    }

    // Post for creating
    public function store(Request $request)
    {
        $fields = Validator::make($request->all(),[
            'ram_name'            => 'required|string',
            'brand'               => 'required|string',
            'ram_type'            => 'required|string',
            'ram_capacity_gb'     => 'required|string|regex:/^\d+(\.\d+)?\s*GB$/i',
            'ram_speed_mhz' => 'required|string|regex:/^\d+(\.\d+)?\s*MHz$/i',
            'cas_latency'         => 'required|string', // Format e.g., "CL18"
            'power_consumption'   => 'required|string|regex:/^\d+(\.\d+)?\s*W$/i',
            'link'                  => 'required|string'
        ]);

        if($fields->fails()){
            return response()->json([
                'message'=>'All fields are mandatory',
                'error'=>$fields->errors()
            ],422);
        }

        $validatedData = $fields->validated();
        $rams = Rams::create($validatedData);

        return response()->json([
            'status'=> true,
            'message'=> 'Created successful',
            'data' => $rams
        ],200);
    }

    // Get request by specific ID
    public function show($ram_id)
    {
        $rams = Rams::find($ram_id);
        if (!$rams) {
            return response()->json([
                'status' => false,
                'message' => 'Ram data not found'
            ], 404);
        }

        $performanceScore = $this->calculateRamPerformance($rams);

        return response()->json([
            'status' => true,
            'message' => 'Ram data found successfully',
            'data' => $rams,
            'performance_score' => $performanceScore
        ], 200);
    }

// Update
public function update(Request $request, $rams)
{
    $fields = Validator::make($request->all(),[
        'ram_name'            => 'required|string',
        'brand'               => 'required|string',
        'ram_type'            => 'required|string',
        'ram_capacity_gb'     => 'required|string|regex:/^\d+(\.\d+)?\s*GB$/i',
        'ram_speed_mhz' => 'required|string|regex:/^\d+(\.\d+)?\s*MHz$/i',
        'cas_latency'         => 'required|string', // Format e.g., "CL18"
        'power_consumption'   => 'required|string|regex:/^\d+(\.\d+)?\s*W$/i',
        'link'                  => 'required|string'
    ]);

    if($fields->fails()){
        return response()->json([
            'message'=>'All fields are mandatory',
            'error'=>$fields->errors()
        ],422);
    }

    $rams = Rams::find($rams);

    if (!$rams) {
        return response()->json([
            'status' => false,
            'message' => 'Ram data not found'
        ], 404);
    }

    $rams->update($fields->validated());

    return response()->json([
        'status' => true,
        'message' => 'Ram Data Updated Successfully',
        'data' => $rams], 201);
    }

    // Delete requests by specific ID
    public function destroy($rams)
    {
        $rams = Rams::find($rams);
        if (!$rams) {
            return response()->json([
                'status' => false,
                'message' => 'Ram Data not found'
            ], 404);
        }

        $rams->delete();

        return response()->json([
            'message' => 'Ram Data Deleted Successfully',
            'data' => $rams
        ], 200);
    }

    private function calculateRamPerformance($rams): float
    {
        // Step 1: Get the highest RAM speed from the database
        $database_max_ram_speed = (float) DB::table('rams')
            ->select(DB::raw('MAX(CAST(SUBSTRING(ram_speed_mhz, 1, LENGTH(ram_speed_mhz) - 4) AS DECIMAL(10,2))) AS max_speed'))
            ->value('max_speed');


        // Step 2: Sanitize and extract the current RAM speed
        $ramSpeed = (float) str_replace(' MHz', '', trim($rams->ram_speed_mhz));


        // Step 3: Calculate a hypothetical performance score
        if ($database_max_ram_speed > 0) {
            $performanceScore = ($ramSpeed / $database_max_ram_speed) * 100; // Score as a percentage
            \Log::info('Performance Score Calculation: ' . $ramSpeed . ' / ' . $database_max_ram_speed . ' = ' . $performanceScore);
        } else {
            \Log::warning('No valid maximum RAM speed found. Returning performance score of 0.');
            return 0;
        }

        // Step 4: Normalize the score and round the result
        return round(min($performanceScore, 100)); // Ensure score is capped at 100
    }


}
