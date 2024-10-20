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
use App\Models\PowerSupplyUnits;
use Illuminate\Http\JsonResponse;

class CompatibilitiesCheckersController extends Controller
{
    protected $compatibilitiesController;

    public function __construct(CompatibilitiesController $compatibilitiesController)
    {
        $this->compatibilitiesController = $compatibilitiesController;
    }

    public function check(Request $request): JsonResponse
    {
        // Validate incoming request with required component IDs
        $validatedData = $request->validate([
            'processor_name'   => 'required|string',
            'motherboard_name' => 'required|string',
            'ram_name'         => 'required|string',
            'gpu_name'         => 'required|string',
            'psu_name'         => 'required|string',
            'case_name'        => 'required|string',
            'cooler_name'      => 'required|string',
            'hdd_name'         => 'nullable|string',
            'ssd_name'         => 'nullable|string',
        ]);

        // Get compatibility data
        $compatibilityData = $this->compatibilitiesController->getCompatibilityData();

        // Perform the compatibility checks
        $feedback = $this->checkComponentCompatibility($validatedData, $compatibilityData);

        // Return structured feedback as JSON
        return response()->json($feedback);
    }

    private function checkComponentCompatibility(array $components, $compatibilityData): array
    {
        $feedback = [
            'is_compatible' => true,
            'issues' => [],
            'components' => []
        ];

        // Fetch models based on provided names
        $motherboard = Motherboards::where('motherboard_name', $components['motherboard_name'])->first();
        $processor = Processors::where('processor_name', $components['processor_name'])->first();
        $ram = Rams::where('ram_name', $components['ram_name'])->first();
        $gpu = Gpus::where('gpu_name', $components['gpu_name'])->first();
        $psu = PowerSupplyUnits::where('psu_name', $components['psu_name'])->first();
        $case = ComputerCases::where('case_name', $components['case_name'])->first();
        $cooler = CpuCoolers::where('cooler_name', $components['cooler_name'])->first();

        // Optional components
        $hdd = isset($components['hdd_name']) ? Hdds::where('hdd_name', $components['hdd_name'])->first() : null;
        $ssd = isset($components['ssd_name']) ? Ssds::where('ssd_name', $components['ssd_name'])->first() : null;

        // Check Processor and Motherboard Compatibility
        if ($processor && $motherboard) {
            if ($processor->socket_type !== $motherboard->socket_type) {
                $feedback['is_compatible'] = false;
                $feedback['issues'][] = "The processor {$processor->processor_name} is not compatible with the motherboard {$motherboard->motherboard_name} due to socket type.";
            }

            // Check chipset compatibility
            if (!empty($processor->compatible_chipsets) && in_array($motherboard->chipset, (array)$processor->compatible_chipsets)) {
                $feedback['is_compatible'] = false;
                $feedback['issues'][] = "The processor {$processor->processor_name} is not compatible with the motherboard {$motherboard->motherboard_name} due to chipset compatibility.";
            }
        }

        // Check RAM Compatibility
        if ($ram && $motherboard) {
           // Check if the motherboard supports multiple RAM types by splitting the supported_ram_type field
           //ram type compatibility
            $supported_ram_types = array_map('trim', explode(',', $motherboard->supported_ram_type));

            if (!in_array($ram->ram_type, $supported_ram_types)) {
                $feedback['is_compatible'] = false;
                $feedback['issues'][] = "The RAM {$ram->ram_name} (Type: {$ram->ram_type}) is not compatible with the motherboard {$motherboard->motherboard_name} (Supported Types: " . implode(', ', $supported_ram_types) . ") due to RAM type.";
            }

            // RAM Speed Compatibility
            $ram_speed_mhz = (int) filter_var($ram->ram_speed_mhz, FILTER_SANITIZE_NUMBER_INT);
            $motherboard_max_ram_speed = (int) filter_var($motherboard->max_ram_speed, FILTER_SANITIZE_NUMBER_INT);
            if ($ram_speed_mhz > $motherboard_max_ram_speed) {
                $feedback['is_compatible'] = false;
                $feedback['issues'][] = "The RAM {$ram->ram_name} exceeds the maximum supported speed of the motherboard {$motherboard->motherboard_name}.";
            }

            // RAM Capacity Compatibility
            $ram_capacity_gb = (int) filter_var($ram->ram_capacity_gb, FILTER_SANITIZE_NUMBER_INT);
            $motherboard_max_ram_capacity = (int) filter_var($motherboard->max_ram_capacity, FILTER_SANITIZE_NUMBER_INT);
            if ($ram_capacity_gb > $motherboard_max_ram_capacity) {
                $feedback['is_compatible'] = false;
                $feedback['issues'][] = "The RAM {$ram->ram_name} exceeds the maximum capacity supported by the motherboard {$motherboard->motherboard_name}.";
            }

            // RAM Slot Compatibility (Assume 1 stick of RAM)
            if ($motherboard->max_ram_slots < 1) {
                $feedback['is_compatible'] = false;
                $feedback['issues'][] = "The motherboard {$motherboard->motherboard_name} does not have enough RAM slots for the selected RAM.";
            }
        }

        // Check GPU Compatibility
        if ($gpu && $psu && $motherboard) {
            // Total power consumption calculation
            $total_gpu_power = (int) filter_var($gpu->required_power, FILTER_SANITIZE_NUMBER_INT);
            $processor_power = (int) filter_var($processor->tdp, FILTER_SANITIZE_NUMBER_INT);
            $ram_power_consumption = (int) filter_var($ram->power_consumption, FILTER_SANITIZE_NUMBER_INT);

    // Calculate total power requirements
    $total_power = $processor_power + (int) filter_var($total_gpu_power, FILTER_SANITIZE_NUMBER_INT) + $ram_power_consumption +
                ($hdd ? (int) filter_var($hdd->power_consumption, FILTER_SANITIZE_NUMBER_INT) : 0) +
                ($ssd ? (int) filter_var($ssd->power_consumption, FILTER_SANITIZE_NUMBER_INT) : 0) +
                ($cooler ? (int) filter_var($cooler->power_consumption, FILTER_SANITIZE_NUMBER_INT) : 0);

            // PSU Efficiency Handling
            $psu_efficiency_factor = match ($psu->efficiency_rating) {
                '80+ Bronze' => 0.82,
                '80+ Silver' => 0.85,
                '80+ Gold' => 0.87,
                '80+ Platinum' => 0.90,
                default => 0.80,
            };

            // Ensure PSU provides enough wattage for total power consumption with headroom
            $psu_continuous_wattage = (int) filter_var($psu->continuous_wattage, FILTER_SANITIZE_NUMBER_INT);
            if ($psu_continuous_wattage * $psu_efficiency_factor < $total_power * 1.2) {
                $feedback['is_compatible'] = false;
                $feedback['issues'][] = "The PSU {$psu->psu_name} provides only " . ($psu_continuous_wattage * $psu_efficiency_factor) . "W, which is insufficient for the total power requirement of " . ($total_power * 1.2) . "W.";
            }

            // PSU Connector Validation
            if ($gpu->required_6_pin_connectors > $psu->gpu_6_pin_connectors) {
                $feedback['is_compatible'] = false;
                $feedback['issues'][] = "The PSU {$psu->psu_name} does not have enough 6-pin connectors for the GPU {$gpu->gpu_name}.";
            }
            if ($gpu->required_8_pin_connectors > $psu->gpu_8_pin_connectors) {
                $feedback['is_compatible'] = false;
                $feedback['issues'][] = "The PSU {$psu->psu_name} does not have enough 8-pin connectors for the GPU {$gpu->gpu_name}.";
            }
            if ($gpu->required_12_pin_connectors && $gpu->required_12_pin_connectors > $psu->gpu_12_pin_connectors) {
                $feedback['is_compatible'] = false;
                $feedback['issues'][] = "The PSU {$psu->psu_name} does not have enough 12-pin connectors for the GPU {$gpu->gpu_name}.";
            }
        }

        // Check Case Compatibility
        $gpu_length_mm = (int) filter_var($gpu->gpu_length_mm, FILTER_SANITIZE_NUMBER_INT);
        $case_max_gpu_length_mm = (int) filter_var($case->max_gpu_length_mm, FILTER_SANITIZE_NUMBER_INT);

        if ($case && $motherboard && $gpu) {
            // Convert form factor supported to array if needed
            $supportedFormFactors = json_decode($case->form_factor_supported, true);

            if (!is_array($supportedFormFactors)) {
                $supportedFormFactors = []; // Ensure it's an empty array if decoding fails
            }

            // Check motherboard form factor compatibility
            if (!in_array($motherboard->form_factor, $supportedFormFactors)) {
                $feedback['is_compatible'] = false;
                $feedback['issues'][] = "The case {$case->case_name} does not support the motherboard form factor {$motherboard->form_factor}.";
            }

            // Check GPU length compatibility with the case
            if ($case_max_gpu_length_mm < $gpu_length_mm) {
                $feedback['is_compatible'] = false;
                $feedback['issues'][] = "The GPU {$gpu->gpu_name} is too long for the case {$case->case_name}.";
            }
        }

        // Check CPU Cooler Compatibility
        if ($cooler && $processor && $case) {
            // Check if cooler supports the processor's socket type
            $supported_sockets = json_decode($cooler->socket_type_supported, true); // Convert JSON array back to PHP array
            if (!in_array($processor->socket_type, $supported_sockets)) {
                $feedback['is_compatible'] = false;
                $feedback['issues'][] = "The cooler {$cooler->name} is not compatible with the processor {$processor->processor_name} due to socket type mismatch.";
            }

        // Check if the cooler fits within the case
        $cooler_max_height_mm = (int) filter_var($cooler->max_cooler_height_mm, FILTER_SANITIZE_NUMBER_INT);
        $case_max_cooler_height_mm = (int) filter_var($cooler->max_cooler_height_mm, FILTER_SANITIZE_NUMBER_INT );
        if ($case_max_cooler_height_mm < $cooler_max_height_mm) {
            $feedback['is_compatible'] = false;
            $feedback['issues'][] = "The cooler {$cooler->cooler_name} is too tall to fit in the case {$case->case_name}. Case supports up to {$case_max_cooler_height_mm}mm, but the cooler height is {$cooler_max_height_mm}mm.";
            }

        // Cooler TDP compatibility check
        $cooler_tdp = (int) filter_var($cooler->tdp_rating, FILTER_SANITIZE_NUMBER_INT);
        $processor_tdp = (int) filter_var($processor->tdp, FILTER_SANITIZE_NUMBER_INT);
        $gpu_tdp = (int) filter_var($gpu->tdp_wattage, FILTER_SANITIZE_NUMBER_INT);

        if ($cooler_tdp < $processor_tdp + $gpu_tdp) {
            $feedback['is_compatible'] = false;
            $feedback['issues'][] = "The cooler {$cooler->cooler_name} (TDP: {$cooler_tdp}W) cannot handle the combined TDP of the processor {$processor->processor_name} ({$processor_tdp}W) and GPU {$gpu->gpu_name} ({$gpu_tdp}W) totaling " . ($processor_tdp + $gpu_tdp) . "W.";
        }

        // Case airflow compatibility
        if ($gpu_tdp > 250) {
            if ($case->airflow_rating != 'high') {
                $feedback['is_compatible'] = false;
                $feedback['issues'][] = "The GPU {$gpu->gpu_name} has a high TDP, but the case {$case->case_name} does not support sufficient airflow.";
            }
        } elseif ($gpu_tdp > 200) {
            if ($case->airflow_rating == 'low') {
                $feedback['is_compatible'] = false;
                $feedback['issues'][] = "The GPU {$gpu->gpu_name} requires moderate airflow, but the case {$case->case_name} has a low airflow rating.";
            } elseif ($case->airflow_rating == 'medium') {
                $feedback['is_compatible'] = true; // Compatible but might run hotter
                $feedback['warnings'][] = "The GPU {$gpu->gpu_name} requires moderate airflow, and the case {$case->case_name} has medium airflow. Consider additional cooling for optimal performance.";
            }
        }



        // Check HDD Compatibility
        if ($hdd && $motherboard) {
            if ($hdd->interface_type === 'M.2' && !$motherboard->has_m2_slot) {
                $feedback['is_compatible'] = false;
                $feedback['issues'][] = "The motherboard {$motherboard->motherboard_name} does not have an M.2 slot for the SSD {$hdd->hdd_name}.";
            }
            elseif ($hdd->interface_type === 'SATA' && !$motherboard->has_sata_ports) {
                $feedback['is_compatible'] = false;
                $feedback['issues'][] = "The motherboard {$motherboard->motherboard_name} does not have SATA ports for the HDD {$hdd->hdd_name}.";
            }
            if ($case->max_hdd_count <= $case->current_hdd_count) {
                $feedback['is_compatible'] = false;
                $feedback['issues'][] = "The case {$case->case_name} cannot accommodate more HDDs.";
            }
        }

        // Check SSD Compatibility
        if ($ssd && $motherboard) {
            if ($ssd->interface_type === 'M.2' && !$motherboard->has_m2_slot) {
                $feedback['is_compatible'] = false;
                $feedback['issues'][] = "The motherboard {$motherboard->motherboard_name} does not have an M.2 slot for the SSD {$ssd->ssd_name}.";
            } elseif ($ssd->interface_type === 'SATA' && !$motherboard->has_sata_ports) {
                $feedback['is_compatible'] = false;
                $feedback['issues'][] = "The motherboard {$motherboard->motherboard_name} does not have SATA ports for the SSD {$ssd->ssd_name}.";
            }
            if ($case->max_ssd_count <= $case->current_ssd_count) {
                $feedback['is_compatible'] = false;
                $feedback['issues'][] = "The case {$case->case_name} cannot accommodate more SSDs.";
            }
        }

        // Add components to feedback
        $feedback['components'] = [
            'processor' => $processor->processor_name,
            'motherboard' => $motherboard->motherboard_name,
            'ram' => $ram->ram_name,
            'gpu' => $gpu->gpu_name,
            'psu' => $psu->psu_name,
            'case' => $case->case_name,
            'cooler' => $cooler->cooler_name,
            'hdd' => $hdd ? $hdd->hdd_name : null,
            'ssd' => $ssd ? $ssd->ssd_name : null,
        ];

        return $feedback;
        }
    }
}
