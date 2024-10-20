<?php

namespace App\Http\Controllers;

use App\Models\Accounts;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AccountsController extends Controller
{
    // Get request to retrieve all accounts
    public function index()
    {
        return Accounts::all();
    }

    // Post for creating a new account
    public function store(Request $request)
    {
        $fields = Validator::make($request->all(), [
            'username' => 'required|string',
            "email" => 'required|email',
            'password' => 'required|min:8|string',
        ]);

        if ($fields->fails()) {
            return response()->json([
                'message' => 'All fields are mandatory',
                'error' => $fields->errors()
            ], 422);
        }

        $validatedData = $fields->validated();
        $accounts = Accounts::create($validatedData);

        return response()->json([
            'status' => true,
            'message' => 'Account created successfully',
            'data' => $accounts
        ], 200);
    }

    // Get request for a specific account by ID
    public function show($id)
    {
        $accounts = Accounts::find($id);

        if (!$accounts) {
            return response()->json([
                'status' => false,
                'message' => 'Account not found'
            ], 404);
        }

        return response()->json([
            'status' => true,
            'message' => 'Account found successfully',
            'data' => $accounts
        ], 200);
    }

    // Update request for a specific account
    public function update(Request $request, $id)
    {
        $fields = Validator::make($request->all(), [
            'username' => 'required|string',
            "email" => 'required|email|unique:accounts,email,' . $id,
            'password' => 'required|min:8|string',
        ]);

        if ($fields->fails()) {
            return response()->json([
                'message' => 'All fields are mandatory',
                'error' => $fields->errors()
            ], 422);
        }

        $accounts = Accounts::find($id);
        if (!$accounts) {
            return response()->json([
                'status' => false,
                'message' => 'Account not found'
            ], 404);
        }

        $accounts->update($fields->validated());

        return response()->json([
            'status' => true,
            'message' => 'Account updated successfully',
            'data' => $accounts
        ], 201);
    }

    // Delete request for a specific account
    public function destroy($id)
    {
        $accounts = Accounts::find($id);
        if (!$accounts) {
            return response()->json([
                'status' => false,
                'message' => 'Account not found'
            ], 404);
        }

        $accounts->delete();

        return response()->json([
            'message' => 'Account deleted successfully',
            'data' => $accounts
        ], 200);
    }
}
