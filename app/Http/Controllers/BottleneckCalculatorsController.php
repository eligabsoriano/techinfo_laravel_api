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
}
