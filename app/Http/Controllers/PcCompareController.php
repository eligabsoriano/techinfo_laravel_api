<?php
namespace App\Http\Controllers;

use App\Models\Gpus;
use App\Models\Hdds;
use App\Models\Rams;
use App\Models\Ssds;
use App\Models\Processors;
use Illuminate\Http\Request;
use App\Models\PowerSupplyUnits;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class PcCompareController extends Controller
{
    public function index(): JsonResponse
    {
        $data = [
            'processors' => Processors::all(),
            'gpuses' => Gpus::all(),
            'rams' => Rams::all(),
            'power_supply_units' => PowerSupplyUnits::all(),
            'ssds' => Ssds::all(),
            'hdds' => Hdds::all()
        ];

        return response()->json($data);
    }

    public function pcCompare(Request $request): JsonResponse
    {
        $fields = $request->validate([
            'build_one.processor_name' => 'required|string',
            'build_one.gpu_name' => 'required|string',
            'build_one.ram_name' => 'required|string',
            'build_one.psu_name' => 'required|string',
            'build_one.ssd_name' => 'nullable|string', // Added SSD
            'build_one.hdd_name' => 'nullable|string', // Added HDD
            'build_two.processor_name' => 'required|string',
            'build_two.gpu_name' => 'required|string',
            'build_two.ram_name' => 'required|string',
            'build_two.psu_name' => 'required|string',
            'build_two.ssd_name' => 'nullable|string', // Added SSD
            'build_two.hdd_name' => 'nullable|string'  // Added HDD
        ]);

        $buildOneComponents = $this->getComponents($fields['build_one']);
        $buildTwoComponents = $this->getComponents($fields['build_two']);

        if (isset($buildOneComponents['error'])) {
            return response()->json(['message' => $buildOneComponents['error']], 404);
        }

        if (isset($buildTwoComponents['error'])) {
            return response()->json(['message' => $buildTwoComponents['error']], 404);
        }

        $scores = $this->calculateScores($buildOneComponents, $buildTwoComponents);

        return response()->json($scores);
    }

    private function getComponents(array $build): array
    {
        $processor = Processors::where('processor_name', $build['processor_name'])->first();
        $gpu = Gpus::where('gpu_name', $build['gpu_name'])->first();
        $ram = Rams::where('ram_name', $build['ram_name'])->first();
        $psu = PowerSupplyUnits::where('psu_name', $build['psu_name'])->first();
        $ssd = !empty($build['ssd_name']) ? Ssds::where('ssd_name', $build['ssd_name'])->first() : null; // Check if SSD name is provided
        $hdd = !empty($build['hdd_name']) ? Hdds::where('hdd_name', $build['hdd_name'])->first() : null; // Check if HDD name is provided

        $errors = [];
        if (!$processor) {
            $errors[] = 'Processor not found: ' . $build['processor_name'];
        }
        if (!$gpu) {
            $errors[] = 'GPU not found: ' . $build['gpu_name'];
        }
        if (!$ram) {
            $errors[] = 'RAM not found: ' . $build['ram_name'];
        }
        if (!$psu) {
            $errors[] = 'Power Supply Unit not found: ' . $build['psu_name'];
        }

        if (!empty($errors)) {
            return ['error' => implode('; ', $errors)];
        }

        return [
            'processor' => $processor,
            'gpu' => $gpu,
            'ram' => $ram,
            'psu' => $psu,
            'ssd' => $ssd, // Return SSD
            'hdd' => $hdd  // Return HDD
        ];
    }

    private function calculateScores(array $buildOne, array $buildTwo): array
    {
        $buildOneValues = $this->getParsedValues($buildOne);
        $buildTwoValues = $this->getParsedValues($buildTwo);

        // Get maximum values for each component type from the database
        $maxTurboBoostClockSpeed = (float) DB::table('processors')
            ->select(DB::raw('MAX(CAST(SUBSTRING(max_turbo_boost_clock_speed, 1, LENGTH(max_turbo_boost_clock_speed) - 4) AS DECIMAL(5,2))) AS max_speed'))
            ->value('max_speed');

        $maxBoostClockGhz = (float) DB::table('gpuses')
            ->select(DB::raw('MAX(CAST(SUBSTRING(boost_clock_ghz, 1, LENGTH(boost_clock_ghz) - 3) AS DECIMAL(5,2))) AS max_clock'))
            ->value('max_clock');

        $maxRamSpeedMhz = (float) DB::table('rams')
            ->select(DB::raw('MAX(CAST(SUBSTRING(ram_speed_mhz, 1, LENGTH(ram_speed_mhz) - 4) AS DECIMAL(10,2))) AS max_speed'))
            ->value('max_speed');

        $maxPsuWattage = (float) DB::table('power_supply_units')
            ->select(DB::raw('MAX(CAST(SUBSTRING(wattage, 1, LENGTH(wattage) - 1) AS DECIMAL(10,2))) AS max_wattage'))
            ->value('max_wattage');

        $maxSsdCapacity = (float) DB::table('ssds')
            ->select(DB::raw('MAX(CAST(SUBSTRING(capacity_gb, 1, LENGTH(capacity_gb) - 2) AS DECIMAL(10,2))) AS max_capacity'))
            ->value('max_capacity');

        $maxHddCapacity = (float) DB::table('hdds')
            ->select(DB::raw('MAX(CAST(SUBSTRING(capacity_gb, 1, LENGTH(capacity_gb) - 2) AS DECIMAL(10,2))) AS max_capacity'))
            ->value('max_capacity');

        return [
            'build_one' => $this->getPercentageScores($buildOneValues, $buildTwoValues, $maxTurboBoostClockSpeed, $maxBoostClockGhz, $maxRamSpeedMhz, $maxPsuWattage, $maxSsdCapacity, $maxHddCapacity),
            'build_two' => $this->getPercentageScores($buildTwoValues, $buildOneValues, $maxTurboBoostClockSpeed, $maxBoostClockGhz, $maxRamSpeedMhz, $maxPsuWattage, $maxSsdCapacity, $maxHddCapacity)
        ];
    }

    private function getParsedValues(array $build): array
    {
        return [
            'max_turbo_boost' => (float) filter_var($build['processor']->max_turbo_boost_clock_speed, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION),
            'boost_clock' => (float) filter_var($build['gpu']->boost_clock_ghz, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION),
            'ram_speed' => (float) str_replace(' MHz', '', trim($build['ram']->ram_speed_mhz)), // Remove " MHz"
            'wattage' => (float) filter_var(trim($build['psu']->wattage), FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION), // e.g., "750W"
            'ssd_capacity' => $build['ssd'] ? (float) str_replace(['GB', ' '], '', trim($build['ssd']->capacity_gb)) : 0, // Use 0 if SSD is null
            'hdd_capacity' => $build['hdd'] ? (float) str_replace(['GB', ' '], '', trim($build['hdd']->capacity_gb)) : 0  // Use 0 if HDD is null
        ];
    }

    private function getPercentageScores(array $currentValues, array $comparisonValues, float $maxTurboBoostClockSpeed, float $maxBoostClockGhz, float $maxRamSpeedMhz, float $maxPsuWattage, float $maxSsdCapacity, float $maxHddCapacity): array
    {
        return [
            'processor_percentage' => $this->calculatePerformanceScore($currentValues['max_turbo_boost'], $maxTurboBoostClockSpeed),
            'gpu_percentage' => $this->calculatePerformanceScore($currentValues['boost_clock'], $maxBoostClockGhz),
            'ram_percentage' => $this->calculateRamPerformanceScore($currentValues['ram_speed'], $maxRamSpeedMhz),
            'psu_percentage' => $this->calculatePerformanceScore($currentValues['wattage'], $maxPsuWattage),
            'ssd_percentage' => $this->calculatePerformanceScore($currentValues['ssd_capacity'], $maxSsdCapacity),
            'hdd_percentage' => $this->calculatePerformanceScore($currentValues['hdd_capacity'], $maxHddCapacity)
        ];
    }

    private function calculateRamPerformanceScore(float $currentRamSpeed, float $maxRamSpeed): int
    {
        if ($maxRamSpeed > 0) {
            return round(min(($currentRamSpeed / $maxRamSpeed) * 100, 100)); // Prevent division by zero
        }
        return 0; // If maxRamSpeed is 0 or negative, return 0%
    }

    private function calculatePerformanceScore(float $currentValues, float $maxValue): int
    {
        return round(min(($currentValues / ($maxValue ?: 1)) * 100, 100)); // Prevent division by zero
    }
}
