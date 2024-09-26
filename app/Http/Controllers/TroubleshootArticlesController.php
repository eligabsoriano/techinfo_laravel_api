<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TroubleshootArticles;
use App\Http\Controllers\TroubleshootArticlesController;

class TroubleshootArticlesController extends Controller
{
    public function index()
    {
       return TroubleshootArticles::all();
    }

    public function store(Request $request)
    {
        $fields = $request->validate([
            'title'=>'required|string',
            'content'=>'required|string'
        ]);


        $troubleshoot = TroubleshootArticles::create($fields);

        return response()->json(['message'=> 'Article created successful'],200);
    }

    public function show($id)
    {
        $troubleshoot = TroubleshootArticles::findOrFail($id);
        return response()->json([
            'status' => true,
            'message' => 'Article found successfully',
            $troubleshoot
        ], 200);
    }



public function update(Request $request, $troubleshootArticles)
{
    $fields = $request->validate([
        'title' => 'required|string',
        'content' => 'required|string'
    ]);

    $troubleshootArticles = TroubleshootArticles::find($troubleshootArticles);

    $troubleshootArticles->update($fields);

    return response()->json($troubleshootArticles, 201);
}



    public function destroy($troubleshootArticles)
    {
        $troubleshootArticles = TroubleshootArticles::find($troubleshootArticles);
        $troubleshootArticles->delete();

        return response()->json([
            'message' => 'Article deleted successfully'
        ], 200);
    }

}




