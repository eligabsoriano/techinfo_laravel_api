<?php

namespace App\Http\Controllers;

use App\Models\Compatibilities;
use App\Models\Processors;
use App\Models\Motherboards;
use App\Models\Rams;
use App\Models\Gpus;
use App\Models\PowerSupplyUnits;
use App\Models\ComputerCases;
use App\Models\CpuCoolers;
use App\Models\Hdds;
use App\Models\Ssds;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CompatibilitiesController extends Controller
{
    public function getCompatibilityData()
    {
        $data = ['compatibilities' => Compatibilities::all()];
        return response()->json($data); // Return all data as JSON
    }

    // Get request
    public function index()
    {
        return Compatibilities::all();
    }

    // Post for creating
    public function store(Request $request)
    {
        $fields = Validator::make($request->all(), [
            'processor_name'   => 'required|string|exists:processors,processor_name',
            'motherboard_name' => 'required|string|exists:motherboards,motherboard_name',
            'ram_name'         => 'required|string|exists:rams,ram_name',
            'gpu_name'         => 'required|string|exists:gpuses,gpu_name',
            'psu_name'         => 'required|string|exists:power_supply_units,psu_name',
            'case_name'        => 'required|string|exists:computer_cases,case_name',
            'cooler_name'      => 'required|string|exists:cpu_coolers,cooler_name',
            'hdd_name'         => 'nullable|string|exists:hdds,hdd_name',
            'ssd_name'         => 'nullable|string|exists:ssds,ssd_name',
        ]);

        if ($fields->fails()) {
            return response()->json([
                'message' => 'All fields are mandatory',
                'error' => $fields->errors()
            ], 422);
        }

        // Retrieve IDs based on names
        $validatedData = $fields->validated();
        $validatedData['processor_id']   = Processors::where('processor_name', $validatedData['processor_name'])->first()->processor_id;
        $validatedData['motherboard_id'] = Motherboards::where('motherboard_name', $validatedData['motherboard_name'])->first()->motherboard_id;
        $validatedData['ram_id']         = Rams::where('ram_name', $validatedData['ram_name'])->first()->ram_id;
        $validatedData['gpu_id']         = Gpus::where('gpu_name', $validatedData['gpu_name'])->first()->gpu_id;
        $validatedData['psu_id']         = PowerSupplyUnits::where('psu_name', $validatedData['psu_name'])->first()->psu_id;
        $validatedData['case_id']        = ComputerCases::where('case_name', $validatedData['case_name'])->first()->case_id;
        $validatedData['cooler_id']      = CpuCoolers::where('cooler_name', $validatedData['cooler_name'])->first()->cooler_id;
        $validatedData['hdd_id']         = $validatedData['hdd_name'] ? Hdds::where('hdd_name', $validatedData['hdd_name'])->first()->hdd_id : null;
        $validatedData['ssd_id']         = $validatedData['ssd_name'] ? Ssds::where('ssd_name', $validatedData['ssd_name'])->first()->ssd_id : null;

        $compatibilities = Compatibilities::create($validatedData);

        return response()->json([
            'status' => true,
            'message' => 'Created successfully',
            'data' => $compatibilities
        ], 200);
    }

    // Get request by specific ID
    public function show($compatibility_id)
    {
        $compatibilities = Compatibilities::find($compatibility_id);
        if (!$compatibilities) {
            return response()->json([
                'status' => false,
                'message' => 'Compatibility data not found'
            ], 404);
        }
        return response()->json([
            'status' => true,
            'message' => 'Compatibility data found successfully',
            'data' => $compatibilities
        ], 200);
    }

    // Update
    public function update(Request $request, $compatibility_id)
    {
        $fields = Validator::make($request->all(), [
            'processor_name'   => 'required|string|exists:processors,processor_name',
            'motherboard_name' => 'required|string|exists:motherboards,motherboard_name',
            'ram_name'         => 'required|string|exists:rams,ram_name',
            'gpu_name'         => 'required|string|exists:gpuses,gpu_name',
            'psu_name'         => 'required|string|exists:power_supply_units,psu_name',
            'case_name'        => 'required|string|exists:computer_cases,case_name',
            'cooler_name'      => 'required|string|exists:cpu_coolers,cooler_name',
            'hdd_name'         => 'nullable|string|exists:hdds,hdd_name',
            'ssd_name'         => 'nullable|string|exists:ssds,ssd_name',
        ]);

        if ($fields->fails()) {
            return response()->json([
                'message' => 'All fields are mandatory',
                'error' => $fields->errors()
            ], 422);
        }

        $compatibility = Compatibilities::find($compatibility_id);
        if (!$compatibility) {
            return response()->json([
                'status' => false,
                'message' => 'Compatibility data not found'
            ], 404);
        }

        // Retrieve IDs based on names
        $validatedData = $fields->validated();
        $validatedData['processor_id']   = Processors::where('processor_name', $validatedData['processor_name'])->first()->processor_id;
        $validatedData['motherboard_id'] = Motherboards::where('motherboard_name', $validatedData['motherboard_name'])->first()->motherboard_id;
        $validatedData['ram_id']         = Rams::where('ram_name', $validatedData['ram_name'])->first()->ram_id;
        $validatedData['gpu_id']         = Gpus::where('gpu_name', $validatedData['gpu_name'])->first()->gpu_id;
        $validatedData['psu_id']         = PowerSupplyUnits::where('psu_name', $validatedData['psu_name'])->first()->psu_id;
        $validatedData['case_id']        = ComputerCases::where('case_name', $validatedData['case_name'])->first()->case_id;
        $validatedData['cooler_id']      = CpuCoolers::where('cooler_name', $validatedData['cooler_name'])->first()->cooler_id;
        $validatedData['hdd_id']         = $validatedData['hdd_name'] ? Hdds::where('hdd_name', $validatedData['hdd_name'])->first()->hdd_id : null;
        $validatedData['ssd_id']         = $validatedData['ssd_name'] ? Ssds::where('ssd_name', $validatedData['ssd_name'])->first()->ssd_id : null;

        $compatibility->update($validatedData);
        return response()->json([
            'status' => true,
            'message' => 'Compatibility Data Updated Successfully',
            'data' => $compatibility
        ], 200);
    }

    // Delete requests by specific ID
    public function destroy($compatibility_id)
    {
        $compatibility = Compatibilities::find($compatibility_id);
        if (!$compatibility) {
            return response()->json([
                'status' => false,
                'message' => 'Compatibility data not found'
            ], 404);
        }
        $compatibility->delete();

        return response()->json([
            'message' => 'Compatibility data deleted successfully'
        ], 200);
    }
}
