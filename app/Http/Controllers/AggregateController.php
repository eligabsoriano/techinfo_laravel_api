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
use Illuminate\Http\JsonResponse;

class AggregateController extends Controller
{
    public function index(): JsonResponse
    {
        // Fetch data from all models
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
        ];

        return response()->json($data); // Return all data as JSON
    }
}
