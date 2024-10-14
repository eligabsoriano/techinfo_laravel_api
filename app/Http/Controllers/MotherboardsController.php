<?php

namespace App\Http\Controllers;

use App\Models\Motherboards;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class MotherboardsController extends Controller
{
    // Get request
    public function index()
    {
       return Motherboards::all();
    }

    // Post for creating
    public function store(Request $request)
    {
        $fields = Validator::make($request->all(), [
            'motherboard_name' => 'required|string',
            'brand'            => 'required|string',
            'socket_type'      => 'required|string',
            'ram_type'         => 'required|string',
            'max_ram_slots'    => 'required|integer',
            'max_ram_capacity'   => 'required|string|regex:/^\d+(\.\d+)?\s*GB$/i',
            'max_ram_speed'      => 'required|string|regex:/^\d+(\.\d+)?\s*MHz$/i',
            'supported_ram_type' => 'required|string',
            'chipset'            => 'required|string',
            'has_pcie_slot'      => 'required|boolean',
            'has_sata_ports'     => 'required|boolean',
            'has_m2_slot'        => 'required|boolean',
            'gpu_interface'    => 'required|string',
            'form_factor'      => 'required|string',
            'link'             => 'nullable|string',
        ]);

        if($fields->fails()){
            return response()->json([
                'message'=>'All fields are mandatory',
                'error'=>$fields->errors()
            ], 422);
        }

        $validatedData = $fields->validated();
        $motherboards = Motherboards::create($validatedData);

        return response()->json([
            'status'=> true,
            'message'=> 'Created successful',
            'data' => $motherboards
        ], 200);
    }

    // Get request by specific ID
    public function show($motherboard_id)
    {
        $motherboards = Motherboards::find($motherboard_id);
        if (!$motherboards) {
            return response()->json([
                'status' => false,
                'message' => 'Motherboard data not found'
            ], 404);
        }
        return response()->json([
            'status' => true,
            'message' => 'Motherboard data found successfully',
            'data' => $motherboards
        ], 200);
    }

    // Update
public function update(Request $request, $motherboards)
{
    $fields = Validator::make($request->all(), [
        'motherboard_name' => 'required|string',
        'brand'            => 'required|string',
        'socket_type'      => 'required|string',
        'ram_type'         => 'required|string',
        'max_ram_slots'    => 'required|integer',
        'max_ram_capacity'   => 'required|string|regex:/^\d+(\.\d+)?\s*GB$/i',
        'max_ram_speed'      => 'required|string|regex:/^\d+(\.\d+)?\s*MHz$/i',
        'supported_ram_type' => 'required|string',
        'chipset'            => 'required|string',
        'has_pcie_slot'      => 'required|boolean',
        'has_sata_ports'     => 'required|boolean',
        'has_m2_slot'        => 'required|boolean',
        'gpu_interface'    => 'required|string',
        'form_factor'      => 'required|string',
        'link'             => 'nullable|string',
    ]);

    if($fields->fails()){
        return response()->json([
            'message'=>'All fields are mandatory',
            'error'=>$fields->errors()
        ], 422);
    }

    $motherboards = Motherboards::find($motherboards);
    if (!$motherboards) {
        return response()->json([
            'status' => false,
            'message' => 'Motherboard data not found'
        ], 404);
    }

    $motherboards->update($fields->validated());

    return response()->json([
        'status' => true,
        'message' => 'Motherboard Data Updated Successfully',
        'data' => $motherboards], 201);

    }

    // Delete requests by specific ID
    public function destroy($motherboards)
    {
        $motherboards = Motherboards::find($motherboards);
        if (!$motherboards) {
            return response()->json([
                'status' => false,
                'message' => 'Motherboard data not found'
            ], 404);
        }
        $motherboards->delete();

        return response()->json([
            'message' => 'Motherboard data deleted successfully',
            'data' => $motherboards
        ], 200);
    }
}
