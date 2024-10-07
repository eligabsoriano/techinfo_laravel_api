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
            'gpu_name'         => 'required|string',
            'brand'            => 'required|string',
            'interface_type'   => 'required|string',
            'tdp_wattage'      => 'required|integer',
            'gpu_length_mm'    => 'required|integer',
            'required_power'   => 'required|integer',
            'required_6_pin_connectors' => 'required|integer', // Add required connector types
            'required_8_pin_connectors' => 'required|integer',
            'required_12_pin_connectors' => 'nullable|integer',
            'link'             => 'nullable|string'
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
        'gpu_name'         => 'required|string',
        'brand'            => 'required|string',
        'interface_type'   => 'required|string',
        'tdp_wattage'      => 'required|integer',
        'gpu_length_mm'    => 'required|integer',
        'required_power'   => 'required|integer',
        'required_6_pin_connectors' => 'required|integer',
        'required_8_pin_connectors' => 'required|integer',
        'required_12_pin_connectors' => 'nullable|integer',
        'link'             => 'nullable|string'
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
