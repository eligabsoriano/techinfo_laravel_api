<?php

namespace App\Http\Controllers;

use App\Models\Gpus;
use App\Models\Processors;
use Illuminate\Http\Request;
use App\Models\ScreenResolutions;
use Illuminate\Http\JsonResponse;
use Exception;

class BottleneckCalculatorsController extends Controller
{
    public function index(): JsonResponse
    {
        try {
            $data = [
                'processors' => Processors::all(),
                'gpuses' => Gpus::all(),
                'screen_resolutions' => ScreenResolutions::all(),
            ];

            return response()->json($data);
        } catch (Exception $e) {
            return response()->json(['message' => 'Failed to retrieve data.'], 500);
        }
    }

    public function calculateBottleneck(Request $request): JsonResponse
    {
        try {
            $fields = $request->validate([
                'processor_name' => 'required|string',
                'gpu_name' => 'required|string',
                'resolutions_name' => 'required|string',
            ]);

            // Get processor, GPU, and resolution from the database
            $processor = Processors::where('processor_name', $fields['processor_name'])->first();
            if (!$processor) {
                return response()->json(['message' => 'Processor not found.'], 404);
            }

            $gpu = Gpus::where('gpu_name', $fields['gpu_name'])->first();
            if (!$gpu) {
                return response()->json(['message' => 'GPU not found.'], 404);
            }

            $resolution = ScreenResolutions::where('resolutions_name', $fields['resolutions_name'])->first();
            if (!$resolution) {
                return response()->json(['message' => 'Resolution not found.'], 404);
            }

            // Define resolution modifiers
            $resolutionModifiers = [
                'Full HD' => 1.0,             // 1920 x 1080
                'Quad HD' => 0.85,            // 2560 x 1440
                'Ultra HD 4K' => 0.7,         // 3840 x 2160
                'Ultra Wide QHD' => 0.8,      // 3440 x 1440
                'Ultra Wide Full HD' => 0.9,  // 2560 x 1080
                '5K' => 0.65,                 // 5120 x 2880
                '8K' => 0.55,                 // 7680 x 4320
                '720p HD' => 1.1,             // 1280 x 720
            ];

            $resolutionModifier = $resolutionModifiers[$resolution->resolutions_name] ?? 1.0;

            // Extract and ensure attributes are numeric
            $cores = (int)$processor->cores;
            $threads = (int)$processor->threads;
            $baseClockSpeed = (float)$processor->base_clock_speed; // in GHz
            $maxTurboBoostClockSpeed = (float)$processor->max_turbo_boost_clock_speed; // in GHz
            $cacheSizeMb = (float)$processor->cache_size_mb;

            $memorySizeGb = (float)$gpu->memory_size_gb;
            $boostClockGhz = (float)$gpu->boost_clock_ghz; // in GHz

            // Calculate CPU score with adjusted weights
            $cpuScore = ($cores * 15) + ($threads * 8) + ($baseClockSpeed * 15) + ($maxTurboBoostClockSpeed * 20) + ($cacheSizeMb * 4);
            $cpuScore *= $resolutionModifier;

            // Calculate GPU score with adjusted weights
            $gpuScore = ($memorySizeGb * 50) + ($boostClockGhz * 30);
            $gpuScore *= $resolutionModifier;

            // Determine bottleneck type
            $bottleneck = $cpuScore < $gpuScore ? 'CPU' : 'GPU';

            // Calculate percentage difference
            $percentageDifference = ($cpuScore > $gpuScore)
            ? (($cpuScore - $gpuScore) / $cpuScore) * 100
            : (($gpuScore - $cpuScore) / $gpuScore) * 100;

            // Format scores and percentage as strings
            $cpuScoreString = number_format($cpuScore, 2, '.', '');
            $gpuScoreString = number_format($gpuScore, 2, '.', '');
            $percentageDifferenceString = number_format($percentageDifference, 2, '.', '');

            // Generate message based on bottleneck type
            $message = $this->generateBottleneckMessage($bottleneck, $percentageDifferenceString, $resolution->resolutions_name);

            return response()->json([
                'bottleneck' => $bottleneck,
                'cpuScore' => $cpuScoreString,
                'gpuScore' => $gpuScoreString,
                'percentage_difference' => $percentageDifferenceString,
                'message' => $message,
            ]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'An error occurred during bottleneck calculation: ' . $e->getMessage()], 500);
        }
    }

    private function generateBottleneckMessage(string $bottleneck, float $percentageDifference, string $resolutionName): string
    {
        if ($percentageDifference < 10) {
            return "The system is perfectly balanced with no noticeable bottleneck at a resolution of $resolutionName. Performance should be optimal across all tasks.";
        } elseif ($percentageDifference < 20) {
            return "The system is well balanced, with a slight bottleneck on the $bottleneck at a resolution of $resolutionName. You might notice minor slowdowns in very demanding tasks, but overall performance should be smooth.";
        } elseif ($percentageDifference < 35) {
            return "There is a moderate bottleneck on the $bottleneck at a resolution of $resolutionName. This could lead to noticeable slowdowns in high-demand applications.";
        } elseif ($percentageDifference < 50) {
            return "The $bottleneck is causing a significant bottleneck at a resolution of $resolutionName. Consider upgrading the $bottleneck for better performance.";
        } else {
            return "The $bottleneck is severely limiting system performance at a resolution of $resolutionName. You may experience poor performance even in less demanding applications. Upgrading the $bottleneck is strongly recommended for a significant boost.";
        }
    }
}
