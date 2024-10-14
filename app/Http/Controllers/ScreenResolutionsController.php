<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ScreenResolutions;
use Illuminate\Support\Facades\Validator;

class ScreenResolutionsController extends Controller
{
    // Get request (Retrieve all screen resolutions)
    public function index()
    {
        return ScreenResolutions::all();
    }

    // Post for creating a new screen resolution
    public function store(Request $request)
    {
        $fields = Validator::make($request->all(), [
            'resolution_size'  => 'required|string',
            'resolutions_name' => 'required|string',
        ]);

        if ($fields->fails()) {
            return response()->json([
                'message' => 'All fields are mandatory',
                'error' => $fields->errors()
            ], 422);
        }

        $validatedData = $fields->validated();
        $screenResolutions = ScreenResolutions::create($validatedData);

        return response()->json([
            'status' => true,
            'message' => 'Screen resolution created successfully',
            'data' => $screenResolutions
        ], 200);
    }

    // Get request by specific ID (Retrieve one screen resolution)
    public function show($screen_resolutions_id)
    {
        $screenResolutions = ScreenResolutions::find($screen_resolutions_id);

        if (!$screenResolutions) {
            return response()->json([
                'status' => false,
                'message' => 'Screen resolution not found'
            ], 404);
        }

        return response()->json([
            'status' => true,
            'message' => 'Screen resolution found successfully',
            'data' => $screenResolutions
        ], 200);
    }

    // Update a screen resolution
    public function update(Request $request, $screen_resolutions_id)
    {
        $fields = Validator::make($request->all(), [
            'resolution_size'  => 'required|string',
            'resolutions_name' => 'required|string',
        ]);

        if ($fields->fails()) {
            return response()->json([
                'message' => 'All fields are mandatory',
                'error' => $fields->errors()
            ], 422);
        }

        $screenResolutions = ScreenResolutions::find($screen_resolutions_id);

        if (!$screenResolutions) {
            return response()->json([
                'status' => false,
                'message' => 'Screen resolution not found'
            ], 404);
        }

        $screenResolutions->update($fields->validated());

        return response()->json([
            'status' => true,
            'message' => 'Screen resolution updated successfully',
            'data' => $screenResolutions
        ], 201);
    }

    // Delete request by specific ID
    public function destroy($screen_resolutions_id)
    {
        $screenResolutions = ScreenResolutions::find($screen_resolutions_id);

        if (!$screenResolutions) {
            return response()->json([
                'status' => false,
                'message' => 'Screen resolution not found'
            ], 404);
        }

        $screenResolutions->delete();

        return response()->json([
            'status' => true,
            'message' => 'Screen resolution deleted successfully'
        ], 200);
    }
}
