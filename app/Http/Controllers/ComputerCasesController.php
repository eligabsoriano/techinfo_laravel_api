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
            'airflow_rating'        => 'required|string|in:low,medium,high',
            'max_cooler_height_mm'  => 'required|string|regex:/^\d+\s*mm$/i',
            'link'                  => 'required|string'
        ]);

        if ($fields->fails()) {
            return response()->json([
                'message' => 'All fields are mandatory',
                'error' => $fields->errors()
            ], 422);
        }

        // Process validated data
        $validatedData = $fields->validated();
        if (!empty($validatedData['form_factor_supported'])) {
            $validatedData['form_factor_supported'] = json_encode(array_map('trim', explode(',', $validatedData['form_factor_supported'])));
        } else {
            $validatedData['form_factor_supported'] = json_encode([]); // Ensure it's an empty JSON array if null
        }
        // Create computer case
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

        // Update by case_name
        public function update(Request $request, $case_name)
        {
            $computer_case = ComputerCases::where('case_name', $case_name)->first();
            if (!$computer_case) {
                return response()->json([
                    'status' => false,
                    'message' => 'Computer Case not found'
                ], 404);
            }

            $fields = Validator::make($request->all(), [
                'case_name'             => 'required|string',
                'brand'                 => 'required|string',
                'form_factor_supported' => 'required|string',
                'max_gpu_length_mm'     => 'required|string|regex:/^\d+\s*mm$/i',
                'max_hdd_count'         => 'required|integer',
                'max_ssd_count'         => 'required|integer',
                'current_hdd_count'     => 'required|integer',
                'current_ssd_count'     => 'required|integer',
                'airflow_rating'        => 'required|string|in:low,medium,high',
                'max_cooler_height_mm'  => 'required|string|regex:/^\d+\s*mm$/i',
                'link'                  => 'required|string'
            ]);

            if ($fields->fails()) {
                return response()->json([
                    'message' => 'All fields are mandatory',
                    'error' => $fields->errors()
                ], 422);
            }

            // Process validated data
            $validatedData = $fields->validated();
            if (!empty($validatedData['form_factor_supported'])) {
                $validatedData['form_factor_supported'] = json_encode(array_map('trim', explode(',', $validatedData['form_factor_supported'])));
            } else {
                $validatedData['form_factor_supported'] = json_encode([]); // Ensure it's an empty JSON array if null
            }

            // Update computer case
            $computer_case->update($validatedData);
            return response()->json([
                'status' => true,
                'message' => 'Computer Case Data Updated Successfully',
                'data' => $computer_case
            ], 200);
        }

    // Delete by case_name
    public function destroy($case_name)
    {
        $computer_case = ComputerCases::where('case_name', $case_name)->first();
        if (!$computer_case) {
            return response()->json([
                'status' => false,
                'message' => 'Computer Case data not found'
            ], 404);
        }

        $computer_case->delete();

        return response()->json([
            'status' => true,
            'message' => 'Computer Case data deleted successfully',
            'data' => $computer_case
        ], 200);
    }

}
