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
use App\Models\TroubleshootArticles;

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
            'screen_resolutions' => ScreenResolutions::all(),
            'hdds' => Hdds::all(),
            'ssds' => Ssds::all(),
            'troubleshoot_articles' => TroubleshootArticles::all()
        ];
        return response()->json($data); // Return all data as JSON
    }
}
