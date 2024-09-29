<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ComputerCases;
use Illuminate\Support\Facades\Validator;

class ComputerCasesController extends Controller
{
    // Get request
    public function index()
    {
       return ComputerCases::all();
    }

    // Post for creating
    public function store(Request $request)
    {
        $fields = Validator::make($request->all(), [
            'case_name'            => 'required|string',
            'brand'                => 'required|string',
            'form_factor_supported' => 'required|string',
            'max_gpu_length_mm'     => 'required|integer',
        ]);

        if($fields->fails()){
            return response()->json([
                'message'=>'All fields are mandatory',
                'error'=>$fields->errors()
            ], 422);
        }

        $validatedData = $fields->validated();
        $computer_cases = ComputerCases::create($validatedData);

        return response()->json([
            'status'=> true,
            'message'=> 'Created successful',
            'data' => $computer_cases
        ], 200);
    }

    // Get request by specific ID
    public function show($case_id)
    {
        $computer_cases = ComputerCases::find($case_id);
        if (!$computer_cases) {
            return response()->json([
                'status' => false,
                'message' => 'Computer Case data not found'
            ], 404);
        }
        return response()->json([
            'status' => true,
            'message' => 'Computer Case data found successfully',
            'data' => $computer_cases
        ], 200);
    }

    //Update
public function update(Request $request, $computer_cases)
{
    $fields = Validator::make($request->all(), [
        'case_name'            => 'required|string',
        'brand'                => 'required|string',
        'form_factor_supported' => 'required|string',
        'max_gpu_length_mm'     => 'required|integer',
    ]);

    if($fields->fails()){
        return response()->json([
            'message'=>'All fields are mandatory',
            'error'=>$fields->errors()
        ], 422);
    }

    $computer_cases = ComputerCases::find($computer_cases);
    if (!$computer_cases) {
        return response()->json([
            'status' => false,
            'message' => 'Computer Case data not found'
        ], 404);
    }

    $computer_cases->update($fields->validated());
    return response()->json([
        'status' => true,
        'message' => 'Computer Case Data Updated Successfully',
        'data' => $computer_cases], 201);
}
    // Delete requests by specific ID
    public function destroy($computer_cases)
    {
        $computer_cases = ComputerCases::find($computer_cases);
        if (!$computer_cases) {
            return response()->json([
                'status' => false,
                'message' => 'Computer Case data not found'
            ], 404);
        }
        $computer_cases->delete();

        return response()->json([
            'message' => 'Computer Cases data deleted successfully',
            'data' => $computer_cases
        ], 200);
    }
}
