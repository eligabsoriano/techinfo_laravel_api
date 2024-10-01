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
            'processor_id'   => 'required|exists:processors,processor_id',
            'motherboard_id' => 'required|exists:motherboards,motherboard_id',
            'ram_id'         => 'required|exists:rams,ram_id',
            'gpu_id'         => 'required|exists:gpuses,gpu_id',
            'psu_id'         => 'required|exists:power_supply_units,psu_id',
            'case_id'        => 'required|exists:computer_cases,case_id',
            'cooler_id'      => 'required|exists:cpu_coolers,cooler_id',
            'hdd_id'         => 'nullable|exists:hdds,hdd_id',
            'ssd_id'         => 'nullable|exists:ssds,ssd_id',
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

        // Fetch models based on provided IDs
        $motherboard = Motherboards::find($components['motherboard_id']);
        $processor = Processors::find($components['processor_id']);
        $ram = Rams::find($components['ram_id']);
        $gpu = Gpus::find($components['gpu_id']);
        $psu = PowerSupplyUnits::find($components['psu_id']);
        $case = ComputerCases::find($components['case_id']);
        $cooler = CpuCoolers::find($components['cooler_id']);

        // Optional components
        $hdd = isset($components['hdd_id']) ? Hdds::find($components['hdd_id']) : null;
        $ssd = isset($components['ssd_id']) ? Ssds::find($components['ssd_id']) : null;

        // Check Processor and Motherboard Compatibility
        if ($processor && $motherboard) {
            if ($processor->socket_type !== $motherboard->socket_type) {
                $feedback['is_compatible'] = false;
                $feedback['issues'][] = "The processor {$processor->name} is not compatible with the motherboard {$motherboard->name} due to socket type.";
            }

            // Check chipset compatibility
            if (is_array($processor->compatible_chipsets) && !in_array($motherboard->chipset, $processor->compatible_chipsets)) {
                $feedback['is_compatible'] = false;
                $feedback['issues'][] = "The processor {$processor->name} is not compatible with the motherboard {$motherboard->name} due to chipset compatibility.";
            }
        }

        // Check RAM Compatibility
        if ($ram && $motherboard) {
            if ($ram->ram_type !== $motherboard->supported_ram_type) {
                $feedback['is_compatible'] = false;
                $feedback['issues'][] = "The RAM {$ram->ram_name} (Type: {$ram->ram_type}) is not compatible with the motherboard {$motherboard->motherboard_name} (Supported Type: {$motherboard->supported_ram_type}) due to RAM type.";
            }
            if ($ram->ram_speed_mhz > $motherboard->max_ram_speed) {
                $feedback['is_compatible'] = false;
                $feedback['issues'][] = "The RAM {$ram->ram_name} exceeds the maximum supported speed of the motherboard {$motherboard->motherboard_name}.";
            }
            if ($ram->ram_capacity_gb > $motherboard->max_ram_capacity) {
                $feedback['is_compatible'] = false;
                $feedback['issues'][] = "The RAM {$ram->ram_name} exceeds the maximum capacity supported by the motherboard {$motherboard->motherboard_name}.";
            }
        }

        // Check GPU Compatibility
        if ($gpu && $psu && $motherboard) {
            if ($gpu->required_power > $psu->wattage) {
                $feedback['is_compatible'] = false;
                $feedback['issues'][] = "The GPU {$gpu->name} requires more power than the PSU can provide.";
            }
            if (!$motherboard->has_pcie_slot) {
                $feedback['is_compatible'] = false;
                $feedback['issues'][] = "The motherboard {$motherboard->name} does not have a PCIe slot for the GPU {$gpu->name}.";
            }
        }

        // Check Power Supply Compatibility
        if ($psu && $processor && $gpu && $ram) {
            $total_power = $processor->power + $gpu->required_power + $ram->power_consumption;
            if ($psu->wattage < $total_power) {
                $feedback['is_compatible'] = false;
                $feedback['issues'][] = "The PSU {$psu->name} does not provide enough wattage for your selected components.";
            }
            if (!$psu->has_required_connectors) {
                $feedback['is_compatible'] = false;
                $feedback['issues'][] = "The PSU {$psu->name} does not have the required connectors for the motherboard or GPU.";
            }
        }

        // Check Case Compatibility
        if ($case && $motherboard && $gpu) {
            if ($case->form_factor_supported !== $motherboard->form_factor) {
                $feedback['is_compatible'] = false;
                $feedback['issues'][] = "The case {$case->name} does not support the motherboard form factor {$motherboard->form_factor}.";
            }
            if ($case->max_gpu_length_mm < $gpu->length_mm) {
                $feedback['is_compatible'] = false;
                $feedback['issues'][] = "The GPU {$gpu->name} is too long for the case {$case->name}.";
            }
        }

        // Check HDD Compatibility
        if ($hdd && $motherboard) {
            if (!$motherboard->has_sata_ports) {
                $feedback['is_compatible'] = false;
                $feedback['issues'][] = "The motherboard {$motherboard->name} does not have SATA ports for the HDD {$hdd->name}.";
            }
            if ($case->max_hdd_count <= $case->current_hdd_count) {
                $feedback['is_compatible'] = false;
                $feedback['issues'][] = "The case {$case->name} cannot accommodate more HDDs.";
            }
        }

        // Check SSD Compatibility
        if ($ssd && $motherboard) {
            if ($ssd->type === 'M.2' && !$motherboard->has_m2_slot) {
                $feedback['is_compatible'] = false;
                $feedback['issues'][] = "The motherboard {$motherboard->name} does not have an M.2 slot for the SSD {$ssd->name}.";
            } elseif ($ssd->type === 'SATA' && !$motherboard->has_sata_ports) {
                $feedback['is_compatible'] = false;
                $feedback['issues'][] = "The motherboard {$motherboard->name} does not have SATA ports for the SSD {$ssd->name}.";
            }
            if ($case->max_ssd_count <= $case->current_ssd_count) {
                $feedback['is_compatible'] = false;
                $feedback['issues'][] = "The case {$case->name} cannot accommodate more SSDs.";
            }
        }

        // Add components to feedback
        $feedback['components'] = [
            'processor' => $processor,
            'motherboard' => $motherboard,
            'ram' => $ram,
            'gpu' => $gpu,
            'psu' => $psu,
            'case' => $case,
            'cooler' => $cooler,
            'hdd' => $hdd,
            'ssd' => $ssd,
        ];

        return $feedback;
    }
}
