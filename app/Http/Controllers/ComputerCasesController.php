<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ComputerCasesController extends Controller
{
    public function index()
    {
       return ComputerCases::all();
    }

    public function store(Request $request)
    {
        $fields = $request->validate([
            'case_name'            => 'required|string',
            'brand'                => 'required|string',
            'form_factor_supported' => 'required|string',
            'max_gpu_length_mm'     => 'required|integer',
        ]);

        $computer_cases = ComputerCases::create($fields);

        return response()->json(['message'=> 'Created successful'],200);
    }

    public function show($id)
    {
        $computer_cases = ComputerCases::findOrFail($id);
        return response()->json([
            'status' => true,
            'message' => 'Computer Case data found successfully',
            'data' => $computer_cases
        ], 200);
    }



public function update(Request $request, $computer_cases)
{
    $fields = $request->validate([
        'case_name'            => 'required|string',
        'brand'                => 'required|string',
        'form_factor_supported' => 'required|string',
        'max_gpu_length_mm'     => 'required|integer',
    ]);

    $computer_cases = ComputerCases::find($computer_cases);

    $computer_cases->update($fields);

    return response()->json($computer_cases, 201);
}



    public function destroy($computer_cases)
    {
        $computer_cases = ComputerCases::find($computer_cases);
        $computer_cases->delete();

        return response()->json([
            'message' => 'Computer Cases data deleted successfully'
        ], 200);
    }
}
