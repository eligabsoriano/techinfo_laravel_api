<?php

namespace App\Http\Controllers;

use App\Models\Processors;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProcessorsController extends Controller
{
    // Get request
    public function index()
    {
       return Processors::all();
    }

    // Post for creating
    public function store(Request $request)
    {
        $fields = Validator::make($request->all(), [
            'processor_name'           => 'required|string',
            'brand'                    => 'required|string',
            'socket_type'              => 'required|string',
            'compatible_chipsets'      => 'nullable|string',
            'cores'                    => 'required|integer',           // Number of cores (e.g., 8)
            'threads'                  => 'required|integer',           // Number of threads (e.g., 16)
            'base_clock_speed'         => 'required|string|regex:/^\d+(\.\d+)?\s*GHz$/i', // Base clock speed in GHz (e.g., 3.5)
            'max_turbo_boost_clock_speed' => 'required|string|regex:/^\d+(\.\d+)?\s*GHz$/i', // Max turbo boost speed in GHz (e.g., 5.3)
            'tdp'                      => 'required|string|regex:/^\d+\s*W$/i', // TDP (e.g., 125W)
            'cache_size_mb'            => 'required|integer',           // Cache size in MB (e.g., 16)
            'integrated_graphics'      => 'nullable|string',            // Integrated graphics (e.g., "Intel UHD Graphics 750")
            'link'                     => 'required|string'
        ]);

        if($fields->fails()){
            return response()->json([
                'message'=>'All fields are mandatory',
                'error'=>$fields->errors()
            ],422);
        }

        $validatedData = $fields->validated();
        // Convert compatible_chipsets to an array if it's a string
        if (!empty($validatedData['compatible_chipsets'])) {
            $validatedData['compatible_chipsets'] = json_encode(array_map('trim', explode(',', $validatedData['compatible_chipsets'])));
        } else {
            $validatedData['compatible_chipsets'] = json_encode([]); // Ensure it's an empty JSON array if null
        }
        $processors = Processors::create($validatedData);

        return response()->json([
            'status'=> true,
            'message'=> 'Created successful',
            'data' => $processors
        ],200);
    }

    // Get request by specific ID
    public function show($processor_id)
    {
        $processors = Processors::find($processor_id);
        if (!$processors) {
            return response()->json([
                'status' => false,
                'message' => 'Processor data not found'
            ], 404);
        }

        return response()->json([
            'status' => true,
            'message' => 'Processors data found successfully',
            'data' => $processors
        ], 200);
    }

    // Update
public function update(Request $request, $processors)
{
    $fields = Validator::make($request->all(),[
        'processor_name'           => 'required|string',
        'brand'                    => 'required|string',
        'socket_type'              => 'required|string',
        'compatible_chipsets'      => 'nullable|string',
        'cores'                    => 'required|integer',           // Number of cores (e.g., 8)
        'threads'                  => 'required|integer',           // Number of threads (e.g., 16)
        'base_clock_speed'         => 'required|string|regex:/^\d+(\.\d+)?\s*GHz$/i', // Base clock speed in GHz (e.g., 3.5)
        'max_turbo_boost_clock_speed' => 'required|string|regex:/^\d+(\.\d+)?\s*GHz$/i', // Max turbo boost speed in GHz (e.g., 5.3)
        'tdp'                      => 'required|string|regex:/^\d+\s*W$/i', // TDP (e.g., 125W)
        'cache_size_mb'            => 'required|integer',           // Cache size in MB (e.g., 16)
        'integrated_graphics'      => 'nullable|string',            // Integrated graphics (e.g., "Intel UHD Graphics 750")
        'link'                     => 'required|string'
    ]);

    if($fields->fails()){
        return response()->json([
            'message'=>'All fields are mandatory',
            'error'=>$fields->errors()
        ],422);
    }

    $processors = Processors::find($processors);
    if (!$processors) {
        return response()->json([
            'status' => false,
            'message' => 'Processor data not found'
        ], 404);
    }

    // Convert compatible_chipsets to an array if it's a string
    if (!empty($validatedData['compatible_chipsets'])) {
        $validatedData['compatible_chipsets'] = json_encode(array_map('trim', explode(',', $validatedData['compatible_chipsets'])));
    } else {
        $validatedData['compatible_chipsets'] = json_encode([]); // Ensure it's an empty JSON array if null
    }

    $processors->update($fields->validated());
    return response()->json([
        'status' => true,
        'message' => 'Processor Data Updated Successfully',
        'data' => $processors], 201);
}

    // Delete requests by specific ID
    public function destroy($processors)
    {
        $processors = Processors::find($processors);
        if (!$processors) {
            return response()->json([
                'status' => false,
                'message' => 'Processors data not found'
            ], 404);
        }
        $processors->delete();

        return response()->json([
            'message' => 'Processors data deleted successfully',
            'data' => $processors
        ], 200);
    }
}
