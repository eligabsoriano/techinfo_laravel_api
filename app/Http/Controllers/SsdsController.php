<?php

namespace App\Http\Controllers;

use App\Models\Ssds;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SsdsController extends Controller
{

    // Get request
    public function index()
    {
       return Ssds::all();
    }

    // Post for creating
    public function store(Request $request)
    {
        $fields = Validator::make($request->all(), [
            'ssd_name'       => 'required|string',
            'brand'          => 'required|string',
            'interface_type' => 'required|string',
            'capacity_gb'    => 'required|string',
            'link'                  => 'required|string'
        ]);

        if($fields->fails()){
            return response()->json([
                'message'=>'All fields are mandatory',
                'error'=>$fields->errors()
            ],422);
        }

        $validatedData = $fields->validated();
        $ssds = Ssds::create($validatedData);

        return response()->json([
            'status' => true,
            'message'=> 'Created successful',
            'data' => $ssds
        ],200);
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

        return response()->json([
            'status' => true,
            'message' => 'SSD data found successfully',
            'data' => $ssds
        ], 200);
    }

    // Update
public function update(Request $request, $ssds)
{
    $fields = Validator::make($request->all(), [
        'ssd_name'       => 'required|string',
        'brand'          => 'required|string',
        'interface_type' => 'required|string',
        'capacity_gb'    => 'required|string',
        'link'                  => 'required|string'
    ]);

    if($fields->fails()){
        return response()->json([
            'message'=>'All fields are mandatory',
            'error'=>$fields->errors()
        ],422);
    }

    $ssds = Ssds::find($ssds);

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
        'data' => $ssds], 201);
}

    // Delete requests by specific ID
    public function destroy($ssds)
    {
        $ssds = Ssds::find($ssds);
        if (!$ssds) {
            return response()->json([
                'status' => false,
                'message' => 'SSD data not found'
            ], 404);
        }

        $ssds->delete();

        return response()->json([
            'message' => 'SSD data deleted successfully',
            "data" => $ssds
        ], 200);
    }
}
