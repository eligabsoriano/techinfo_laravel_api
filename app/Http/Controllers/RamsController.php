<?php

namespace App\Http\Controllers;

use App\Models\Rams;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RamsController extends Controller
{
    // Get request
    public function index()
    {
       return Rams::all();
    }

    // Post for creating
    public function store(Request $request)
    {
        $fields = Validator::make($request->all(),[
            'ram_name'            => 'required|string',
            'brand'               => 'required|string',
            'ram_type'            => 'required|string',
            'ram_capacity_gb'     => 'required|string|regex:/^\d+(\.\d+)?\s*GB$/i',
            'ram_speed_mhz'       => 'required|string|regex:/^\d+(\.\d+)?\s*MHz$/i',
            'cas_latency'         => 'required|string', // Format e.g., "CL18"
            'power_consumption'   => 'required|string|regex:/^\d+(\.\d+)?\s*W$/i',
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

        return response()->json([
            'status' => true,
            'message' => 'Ram data found successfully',
            'data' => $rams
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
        'ram_speed_mhz'       => 'required|string|regex:/^\d+(\.\d+)?\s*MHz$/i',
        'cas_latency'         => 'required|string', // Format e.g., "CL18"
        'power_consumption'   => 'required|string|regex:/^\d+(\.\d+)?\s*W$/i',
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
}
