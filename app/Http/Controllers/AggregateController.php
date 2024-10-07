<?php

namespace App\Http\Controllers;

use App\Models\Gpus;
use App\Models\Hdds;
use App\Models\Rams;
use App\Models\Ssds;
use App\Models\CpuCoolers;
use App\Models\Processors;
use App\Models\Motherboards;
use Illuminate\Http\Request;
use App\Models\ComputerCases;
use App\Models\Compatibilities;
use App\Models\PowerSupplyUnits;
use App\Models\ScreenResolutions;
use Illuminate\Http\JsonResponse;

class AggregateController extends Controller
{
    // Fetch all data across all models
    public function index(): JsonResponse
    {
        $data = [
            'processors' => Processors::all(),
            'motherboards' => Motherboards::all(),
            'rams' => Rams::all(),
            'gpuses' => Gpus::all(),
            'power_supply_units' => PowerSupplyUnits::all(),
            'computer_cases' => ComputerCases::all(),
            'cpu_coolers' => CpuCoolers::all(),
            'hdds' => Hdds::all(),
            'ssds' => Ssds::all(),
            'compatibilities' => Compatibilities::all(),
            'screen_resolutions' => ScreenResolutions::all()
        ];

        return response()->json($data);
    }

    // Fetch all data from a specific model
    public function getModelData($modelType): JsonResponse
    {
        $modelData = null;

        // Determine which model to query
        switch ($modelType) {
            case 'processors':
                $modelData = Processors::all();
                break;
            case 'motherboards':
                $modelData = Motherboards::all();
                break;
            case 'rams':
                $modelData = Rams::all();
                break;
            case 'gpuses':
                $modelData = Gpus::all();
                break;
            case 'power_supply_units':
                $modelData = PowerSupplyUnits::all();
                break;
            case 'computer_cases':
                $modelData = ComputerCases::all();
                break;
            case 'cpu_coolers':
                $modelData = CpuCoolers::all();
                break;
            case 'hdds':
                $modelData = Hdds::all();
                break;
            case 'ssds':
                $modelData = Ssds::all();
                break;
            case 'compatibilities':
                $modelData = Compatibilities::all();
                break;
            default:
                return response()->json(['error' => 'Invalid model type'], 400);
        }

        // Return the data of the requested model
        return response()->json($modelData);
    }
}
