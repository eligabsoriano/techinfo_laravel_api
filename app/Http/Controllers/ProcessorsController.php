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
            'processor_name'     => 'required|string',
            'brand'              => 'required|string',
            'socket_type'        => 'required|string',
            'compatible_chipsets' => 'nullable|string',
            'power'              => 'required|integer',
            'base_clock_speed'   => 'required|numeric',
            'max_clock_speed'    => 'required|numeric',
            'link'               => 'nullable|string',
        ]);

        if($fields->fails()){
            return response()->json([
                'message'=>'All fields are mandatory',
                'error'=>$fields->errors()
            ],422);
        }

        $validatedData = $fields->validated();
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
        'processor_name'     => 'required|string',
        'brand'              => 'required|string',
        'socket_type'        => 'required|string',
        'compatible_chipsets' => 'nullable|string',
        'power'              => 'required|integer',
        'base_clock_speed'   => 'required|numeric',
        'max_clock_speed'    => 'required|numeric',
        'link'               => 'nullable|string',
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
