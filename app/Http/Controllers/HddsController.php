<?php

namespace App\Http\Controllers;

use App\Models\Hdds;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class HddsController extends Controller
{
    // Get request
    public function index()
    {
       return Hdds::all();
    }

    // Post for creating
    public function store(Request $request)
    {
        $fields = Validator::make($request->all(), [
            'hdd_name'       => 'required|string',
            'brand'          => 'required|string',
            'interface_type' => 'required|string',
            'capacity_gb'    => 'required|integer',
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
        return response()->json([
            'status' => true,
            'message' => 'HDD data found successfully',
            'data' => $hdds
        ], 200);
    }

    //Update
public function update(Request $request, $hdds)
{
    $fields = Validator::make($request->all(), [
        'hdd_name'       => 'required|string',
        'brand'          => 'required|string',
        'interface_type' => 'required|string',
        'capacity_gb'    => 'required|integer',
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
}
