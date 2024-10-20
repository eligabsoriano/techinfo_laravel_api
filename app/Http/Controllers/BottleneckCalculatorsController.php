<?php

namespace App\Http\Controllers;

use App\Models\Gpus;
use App\Models\Processors;
use Illuminate\Http\Request;
use App\Models\ScreenResolutions;
use Illuminate\Http\JsonResponse;

class BottleneckCalculatorsController extends Controller
{
    public function index(): JsonResponse
    {
        $data = [
            'processors' => Processors::all(),
            'gpuses' => Gpus::all(),
            'screen_resolutions' => ScreenResolutions::all(),
        ];

        return response()->json($data); // Return the selected data as JSON
    }

    public function calculateBottleneck(Request $request): JsonResponse
    {
        // Validate incoming data
        $fields = $request->validate([
            'processor_name' => 'required|string',
            'gpu_name' => 'required|string',
            'resolutions_name' => 'required|string',
        ]);

        // Check if the processor exists by name
        $processor = Processors::where('processor_name', $fields['processor_name'])->first();
        if (!$processor) {
            return response()->json(['message' => 'Processor not found.'], 404);
        }

        // Check if the GPU exists by name
        $gpu = Gpus::where('gpu_name', $fields['gpu_name'])->first();
        if (!$gpu) {
            return response()->json(['message' => 'GPU not found.'], 404);
        }

        // Check if the resolution exists by name
        $resolution = ScreenResolutions::where('resolutions_name', $fields['resolutions_name'])->first();
        if (!$resolution) {
            return response()->json(['message' => 'Resolution not found.'], 404);
        }

        // Convert clock speeds to numeric values
        $cpuClockSpeed = floatval(preg_replace('/[^0-9.]/', '', $processor->base_clock_speed));
        $gpuClockSpeed = floatval(preg_replace('/[^0-9.]/', '', $gpu->boost_clock_ghz));

        // Calculate scores based on components
        $cpuScore = ($processor->cores * 1.5) + ($processor->threads * 1.2) + ($cpuClockSpeed * 2);
        $gpuScore = ($gpu->memory_size_gb * 2) + ($gpuClockSpeed * 3);

        // Adjust based on resolution using a mapping
        $resolutionModifiers = [
            'Full HD' => 1.0,      // 1920x1080
            'Quad HD' => 0.8,      // 2560x1440
            'Ultra HD 4K' => 0.6,  // 3840x2160
            // Add more resolutions here if needed
        ];

        // Get the modifier based on resolution name
        $resolutionModifier = $resolutionModifiers[$resolution->resolutions_name] ?? 1.0; // Default to 1.0 if not found

        $adjustedGpuScore = $gpuScore * $resolutionModifier;

        // Determine bottleneck
        $bottleneck = $cpuScore > $adjustedGpuScore ? 'GPU' : 'CPU';

        // Calculate overall percentage difference
        $percentageDifference = abs(($cpuScore - $adjustedGpuScore) / max($cpuScore, $adjustedGpuScore)) * 100;

        // Format values to two decimal places
        $cpuScore = number_format($cpuScore, 2);
        $adjustedGpuScore = number_format($adjustedGpuScore, 2);
        $percentageDifference = number_format($percentageDifference, 2);

        // Generate a message for the user
        $message = $this->generateBottleneckMessage($bottleneck, $percentageDifference);

        return response()->json([
            'bottleneck' => $bottleneck,
            'cpuScore' => (float)$cpuScore, // Ensure it's returned as a float
            'gpuScore' => (float)$adjustedGpuScore, // Ensure it's returned as a float
            'resolution_modifier' => (float)$resolutionModifier, // Ensure it's returned as a float
            'percentage_difference' => (float)$percentageDifference, // Ensure it's returned as a float
            'message' => $message, // Include the user-friendly message
        ]);
    }

    private function generateBottleneckMessage(string $bottleneck, float $percentageDifference): string
    {
        if ($percentageDifference < 10) {
            return "The system is well balanced with minimal bottleneck. Performance should be consistent.";
        } elseif ($percentageDifference < 25) {
            return "There is a moderate bottleneck on the $bottleneck, which may cause some performance limitations in certain applications.";
        } else {
            return "The $bottleneck is causing a significant bottleneck in the system, which could impact overall performance. Consider upgrading the $bottleneck to improve performance.";
        }
    }
}
