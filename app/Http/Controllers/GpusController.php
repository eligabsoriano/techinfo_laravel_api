<?php

namespace App\Http\Controllers;

use App\Models\Gpus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class GpusController extends Controller
{
    // Get request
    public function index()
    {
       return Gpus::all();
    }

    // Post for creating
    public function store(Request $request)
    {
        $fields = Validator::make($request->all(), [
            'gpu_name'               => 'required|string',            // GPU Name (e.g., RTX 4090 or RX 6600)
            'brand'                  => 'required|string',            // Brand (e.g., NVIDIA or AMD)
            'cuda_cores'             => 'nullable|integer',           // For NVIDIA GPUs (e.g., 16,384 CUDA cores)
            'compute_units'          => 'nullable|integer',           // For AMD GPUs (e.g., 36 Compute Units)
            'stream_processors'      => 'nullable|integer',           // For AMD GPUs (e.g., 2,304 Stream Processors)
            'game_clock_ghz'         => 'nullable|string|regex:/^\d+(\.\d+)?\s*GHz$/i', // new
            'base_clock_ghz'         => 'nullable|string|regex:/^\d+(\.\d+)?\s*GHz$/i', // Base clock (e.g., "2.23 GHz")
            'boost_clock_ghz'        => 'required|string|regex:/^\d+(\.\d+)?\s*GHz$/i', // Boost clock (e.g., "1.37 GHz")
            'memory_size_gb'         => 'required|integer',           // Memory size (e.g., 24 GB or 6 GB)
            'memory_type'            => 'required|string',            // Memory type (e.g., GDDR6X or GDDR6)
            'memory_interface_bits'  => 'required|string|regex:/^\d+-bit$/i', // Memory Interface (e.g., 384-bit or 192-bit)
            'tdp_wattage'            => 'required|string|regex:/^\d+\s*W$/i',  // TDP as string with 'W' (e.g., 450W or 150W)
            'gpu_length_mm'          => 'required|string|regex:/^\d+\s*mm$/i',           // GPU length in mm
            'required_power'         => 'required|string|regex:/^\d+\s*W$/i',           // Required PSU power
            'required_6_pin_connectors' => 'required|integer',        // Required 6-pin connectors
            'required_8_pin_connectors' => 'required|integer',        // Required 8-pin connectors
            'required_12_pin_connectors' => 'nullable|integer',       // Optional 12-pin connectors
        ]);

        if($fields->fails()){
            return response()->json([
                'message'=>'All fields are mandatory',
                'error'=>$fields->errors()
            ], 422);
        }

        $validatedData = $fields->validated();
        $gpuses = Gpus::create($validatedData);

        return response()->json([
            'status'=> true,
            'message'=> 'Created successful',
            'data' => $gpuses
        ], 200);
}

    // Get request by specific ID
    public function show($gpu_id)
    {
        $gpuses = Gpus::find($gpu_id);
        if (!$gpuses) {
            return response()->json([
                'status' => false,
                'message' => 'GPU data not found'
            ], 404);
        }
        return response()->json([
            'status' => true,
            'message' => 'GPU data found successfully',
            'data' => $gpuses
        ], 200);
    }


    //Update
public function update(Request $request, $gpuses)
{
    $fields = Validator::make($request->all(), [
        'gpu_name'               => 'required|string',            // GPU Name (e.g., RTX 4090 or RX 6600)
        'brand'                  => 'required|string',            // Brand (e.g., NVIDIA or AMD)
        'cuda_cores'             => 'nullable|integer',           // For NVIDIA GPUs (e.g., 16,384 CUDA cores)
        'compute_units'          => 'nullable|integer',           // For AMD GPUs (e.g., 36 Compute Units)
        'stream_processors'      => 'nullable|integer',           // For AMD GPUs (e.g., 2,304 Stream Processors)
        'game_clock_ghz'         => 'nullable|string|regex:/^\d+(\.\d+)?\s*GHz$/i', // new
        'base_clock_ghz'         => 'nullable|string|regex:/^\d+(\.\d+)?\s*GHz$/i', // Base clock (e.g., "2.23 GHz")
        'boost_clock_ghz'        => 'required|string|regex:/^\d+(\.\d+)?\s*GHz$/i', // Boost clock (e.g., "1.37 GHz")
        'memory_size_gb'         => 'required|integer',           // Memory size (e.g., 24 GB or 6 GB)
        'memory_type'            => 'required|string',            // Memory type (e.g., GDDR6X or GDDR6)
        'memory_interface_bits'  => 'required|string|regex:/^\d+-bit$/i', // Memory Interface (e.g., 384-bit or 192-bit)
        'tdp_wattage'            => 'required|string|regex:/^\d+\s*W$/i',  // TDP as string with 'W' (e.g., 450W or 150W)
        'gpu_length_mm'          => 'required|string|regex:/^\d+\s*mm$/i',           // GPU length in mm
        'required_power'         => 'required|string|regex:/^\d+\s*W$/i',           // Required PSU power
        'required_6_pin_connectors' => 'required|integer',        // Required 6-pin connectors
        'required_8_pin_connectors' => 'required|integer',        // Required 8-pin connectors
        'required_12_pin_connectors' => 'nullable|integer',       // Optional 12-pin connectors
    ]);

    if($fields->fails()){
        return response()->json([
            'message'=>'All fields are mandatory',
            'error'=>$fields->errors()
        ], 422);
    }

    $gpuses = Gpus::find($gpuses);
    if (!$gpuses) {
        return response()->json([
            'status' => false,
            'message' => 'GPU data not found'
        ], 404);
    }

    $gpuses->update($fields->validated());
    return response()->json([
        'status' => true,
        'message' => 'GPU Data Updated Successfully',
        'data' => $gpuses], 201);
}
    // Delete requests by specific ID
    public function destroy($gpuses)
    {
        $gpuses = Gpus::find($gpuses);
        if (!$gpuses) {
            return response()->json([
                'status' => false,
                'message' => 'GPU data not found'
            ], 404);
        }
        $gpuses->delete();

        return response()->json([
            'message' => 'Gpu data deleted successfully',
            'data' => $gpuses
        ], 200);
    }
}
