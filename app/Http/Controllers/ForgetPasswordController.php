<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Mail\ResetPass;
use App\Models\Accounts;
use Illuminate\Http\Request;
use App\Models\ForgetPassword;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;

class ForgetPasswordController extends Controller
{
    // Step 1: Request for OTP and Token
    public function requestReset(Request $request)
    {
        try {
            $ValidateData = $request->validate([
                'email' => 'required|email|string',
            ], [
                'email.required' => 'The email field is required.',
                'email.email' => 'Please enter a valid email address.',
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'errors' => $e->errors()
            ], 422); // Return 422 Unprocessable Entity for validation errors
        }

        $user = Accounts::where('email', $request->email)->first();

        if (!$user) {
            return response()->json(['message' => 'No record found, try a different email'], 404);
        }

        // Generate a 4-digit random OTP
        $resetPasswordOtp = str_pad(random_int(1000, 9999), 4, '0', STR_PAD_LEFT);

        // Check if the user already requested a password reset
        $userPassReset = ForgetPassword::where('email', $user->email)->first();

        if (!$userPassReset) {
            ForgetPassword::create([
                'email' => $user->email,
                'token' => $resetPasswordOtp,
                'created_at' => Carbon::now(),
            ]);
        } else {
            // Update token and timestamp if a reset request exists
            $userPassReset->update([
                'token' => $resetPasswordOtp,
                'created_at' => Carbon::now(),
            ]);
        }

        // Send OTP to user's email
        Mail::to($user->email)->send(new ResetPass($resetPasswordOtp));

        return response()->json([
            'message' => 'An OTP has been sent to your email.'
        ], 200);
    }

    // Step 2: Reset Password
    public function resetPassword(Request $request)
    {
        try {
            $ValidData = $request->validate([
                'email' => 'required|email|string',
                'token' => 'required|string',
                'password' => 'required|string|min:8',
            ], [
                'email.required' => 'The email field is required.',
                'email.email' => 'Please enter a valid email address.',
                'token.required' => 'The OTP is required.',
                'password.required' => 'The password field is required.',
                'password.min' => 'The password must be at least 8 characters.',
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'errors' => $e->errors()
            ], 422);
        }

        // Find the user by email
        $user = Accounts::where('email', $ValidData['email'])->first();

        if (!$user) {
            return response()->json(['message' => 'No record found, try another email'], 404);
        }

        // Find the reset request by email
        $resetRequest = ForgetPassword::where('email', $user->email)->first();

        // Check if the reset request exists and if the token matches
        if (!$resetRequest || $resetRequest->token != $ValidData['token']) {
            return response()->json(['message' => 'Wrong OTP, please try again'], 400);
        }

        // Check if the OTP is still valid (within 5 minutes)
        if (Carbon::now()->diffInMinutes($resetRequest->created_at) > 5) {
            $resetRequest->delete();
            return response()->json(['message' => 'OTP has expired, please request a new one'], 400);
        }

        // Update the user's password directly
        $user->password = $ValidData['password'];
        $user->save();

        // Delete the used reset token
        $resetRequest->delete();

        return response()->json([
            'message' => 'Password reset successful',
        ], 200);
    }
}
