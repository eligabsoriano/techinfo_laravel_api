<?php

namespace App\Http\Controllers;

use App\Models\CpuCoolers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CpuCoolersController extends Controller
{
    // Get request
    public function index()
    {
       return CpuCoolers::all();
    }

    // Post for creating
    public function store(Request $request)
    {
        $fields = Validator::make($request->all(), [
            'cooler_name'           => 'required|string',
            'brand'                 => 'required|string',
            'socket_type_supported' => 'required|string',
            'max_cooler_height_mm'  => 'required|string|regex:/^\d+\s*mm$/i',
            'tdp_rating'            => 'required|string|regex:/^\d+\s*W$/i',
            'link'                  => 'required|string'
        ]);

        if($fields->fails()){
            return response()->json([
                'message'=>'All fields are mandatory',
                'error'=>$fields->errors()
            ], 422);
        }

        $validatedData = $fields->validated();
        if (!empty($validatedData['socket_type_supported'])) {
            $validatedData['socket_type_supported'] = json_encode(array_map('trim', explode(',', $validatedData['socket_type_supported'])));
        } else {
            $validatedData['socket_type_supported'] = json_encode([]); // Ensure it's an empty JSON array if null
        }
        $cpu_coolers = CpuCoolers::create($validatedData);

        return response()->json([
            'status'=> true,
            'message'=> 'Created successful',
            'data' => $cpu_coolers
        ], 200);
    }

    // Get request by specific ID
    public function show($cooler_id)
    {
        $cpu_coolers = CpuCoolers::find($cooler_id);
        if (!$cpu_coolers) {
            return response()->json([
                'status' => false,
                'message' => 'CPU Cooler data not found'
            ], 404);
        }
        return response()->json([
            'status' => true,
            'message' => 'CPU Cooler data found successfully',
            'data' => $cpu_coolers
        ], 200);
    }

    //Update
public function update(Request $request, $cpu_coolers)
{
    $fields = Validator::make($request->all(), [
        'cooler_name'           => 'required|string',
        'brand'                 => 'required|string',
        'socket_type_supported' => 'required|string',
        'max_cooler_height_mm'  => 'required|string|regex:/^\d+\s*mm$/i',
        'tdp_rating'            => 'required|string|regex:/^\d+\s*W$/i',
        'link'                  => 'required|string'
    ]);

    if($fields->fails()){
        return response()->json([
            'message'=>'All fields are mandatory',
            'error'=>$fields->errors()
        ], 422);
    }

    $cpu_coolers = CpuCoolers::find($cpu_coolers);
    if (!$cpu_coolers) {
        return response()->json([
            'status' => false,
            'message' => 'CPU Cooler data not found'
        ], 404);
    }

    $validatedData = $fields->validated();
    if (!empty($validatedData['socket_type_supported'])) {
        $validatedData['socket_type_supported'] = json_encode(array_map('trim', explode(',', $validatedData['socket_type_supported'])));
    } else {
        $validatedData['socket_type_supported'] = json_encode([]); // Ensure it's an empty JSON array if null
    }

    $cpu_coolers->update($fields->validated());
    return response()->json([
        'status' => true,
        'message' => 'CPU Cooler Data Updated Successfully',
        'data' => $cpu_coolers], 201);
}

    // Delete requests by specific ID
    public function destroy($cpu_coolers)
    {
        $cpu_coolers = CpuCoolers::find($cpu_coolers);
        if (!$cpu_coolers) {
            return response()->json([
                'status' => false,
                'message' => 'CPU Cooler data not found'
            ], 404);
        }
        $cpu_coolers->delete();

        return response()->json([
            'message' => 'Cpu Cooler data deleted successfully',
            'data' => $cpu_coolers
        ], 200);
    }
}
