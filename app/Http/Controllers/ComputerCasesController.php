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
            'case_name'             => 'required|string',
            'brand'                 => 'required|string',
            'form_factor_supported' => 'required|string',
            'max_gpu_length_mm'     => 'required|string|regex:/^\d+\s*mm$/i',
            'max_hdd_count'         => 'required|integer',
            'max_ssd_count'         => 'required|integer',
            'current_hdd_count'     => 'required|integer',
            'current_ssd_count'     => 'required|integer',
            'airflow_rating'        => 'required|string|in:low,medium,high', // Add airflow rating for TDP compatibility checks
            'max_cooler_height_mm'  => 'required|string|regex:/^\d+\s*mm$/i',
            'link'                  => 'required|string'
        ]);

        if($fields->fails()){
            return response()->json([
                'message'=>'All fields are mandatory',
                'error'=>$fields->errors()
            ], 422);
        }

        $validatedData = $fields->validated();
        if (!empty($validatedData['form_factor_supported'])) {
            $validatedData['form_factor_supported'] = json_encode(array_map('trim', explode(',', $validatedData['form_factor_supported'])));
        } else {
            $validatedData['form_factor_supported'] = json_encode([]); // Ensure it's an empty JSON array if null
        }
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
        'case_name'             => 'required|string',
        'brand'                 => 'required|string',
        'form_factor_supported' => 'required|string',
        'max_gpu_length_mm'     => 'required|string|regex:/^\d+\s*mm$/i',
        'max_hdd_count'         => 'required|integer',
        'max_ssd_count'         => 'required|integer',
        'current_hdd_count'     => 'required|integer',
        'current_ssd_count'     => 'required|integer',
        'airflow_rating'        => 'required|string|in:low,medium,high', // Add airflow rating for TDP compatibility checks
        'max_cooler_height_mm'  => 'required|string|regex:/^\d+\s*mm$/i',
        'link'                  => 'required|string'
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

    if (!empty($validatedData['form_factor_supported'])) {
        $validatedData['form_factor_supported'] = json_encode(array_map('trim', explode(',', $validatedData['form_factor_supported'])));
    } else {
        $validatedData['form_factor_supported'] = json_encode([]); // Ensure it's an empty JSON array if null
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
