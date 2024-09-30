<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TroubleshootArticles;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\TroubleshootArticlesController;

class TroubleshootArticlesController extends Controller
{

    // Get request
    public function index()
    {
       return TroubleshootArticles::all();
    }

    // Post for creating
    public function store(Request $request)
    {
        $fields = Validator::make($request->all(), [
            'title'         => 'required|string',
            'content'       => 'required|string',
            'video_embed'   => 'nullable|string'
        ]);

        if($fields->fails()){
            return response()->json([
                'message'=>'All fields are mandatory',
                'error'=>$fields->errors()
            ], 422);
        }

        $validatedData = $fields->validated();
        $troubleshoot = TroubleshootArticles::create($validatedData);

        return response()->json([
            'message'=> 'Article created successful',
            'data' => $troubleshoot
        ], 200);
    }

    // Get request by specific ID
    public function show($id)
    {
        $troubleshoot = TroubleshootArticles::find($id);

        if (!$troubleshoot) {
            return response()->json([
                'status' => false,
                'message' => 'Article not found'
            ], 404);
        }

        return response()->json([
            'status' => true,
            'message' => 'Article found successfully',
            "data" => $troubleshoot
        ], 200);
    }

    // Update
public function update(Request $request, $troubleshootArticles)
{
    $fields = Validator::make($request->all(), [
            'title'         => 'required|string',
            'content'       => 'required|string',
            'video_embed'   => 'nullable|string'
    ]);

    if($fields->fails()){
        return response()->json([
            'message'=>'All fields are mandatory',
            'error'=>$fields->errors()
        ], 422);
    }


    $troubleshootArticles = TroubleshootArticles::find($troubleshootArticles);

    if (!$troubleshootArticles) {
        return response()->json([
            'status' => false,
            'message' => 'Article not found'
        ], 404);
    }

   $troubleshootArticles->update($fields->validated());

    return response()->json([
        'status' => true,
        'message' => 'Article Updated Successfully',
        'data' => $troubleshootArticles], 201);
}

    // Delete requests by specific ID
    public function destroy($troubleshootArticles)
    {
        $troubleshootArticles = TroubleshootArticles::find($troubleshootArticles);

        if (!$troubleshootArticles) {
            return response()->json([
                'status' => false,
                'message' => 'Article not found'
            ], 404);
        }

        $troubleshootArticles->delete();

        return response()->json([
            'message' => 'Article deleted successfully'
        ], 200);
    }
}
