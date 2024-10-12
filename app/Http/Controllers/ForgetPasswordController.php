<?php

namespace App\Http\Controllers;

use App\Mail\ResetPass;
use App\Models\Accounts;
use Illuminate\Http\Request;
use App\Models\Accounts;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash; // Correctly import Hash
use App\Mail\ResetPass;
use App\Models\ForgetPassword;

class ForgetPasswordController extends Controller
{
    // Step 1: Request for OTP and Token
    public function requestReset(Request $request)
    {
        $ValidateData = $request->validate([
            'email' => 'required|email|string',
        ]);

        $user = Accounts::where('email', $request->email)->first();

        if (!$user) {
            return response()->json(['message' => 'No record found, try a different email'], 404);
        }

        // Generate a 4-digit random token to reset the password
        $resetPasswordOtp = str_pad(random_int(1000, 9999), 4, '0', STR_PAD_LEFT); // Ensure a 4-digit OTP

        // Check if the user already requested a password reset
        $userPassReset = ForgetPassword::where('email', $user->email)->first();

        if (!$userPassReset) {
            // Create a new reset token with a timestamp
            ForgetPassword::create([
                'email' => $user->email,
                'token' => $resetPasswordOtp,
            ]);
        } else {
            // Update the reset token with a new timestamp
            ForgetPassword::where('email', $user->email)->update([
                'token' => $resetPasswordOtp,
            ]);
        }

        // Send OTP and Token to email
        Mail::to($user->email)->send(new ResetPass($resetPasswordOtp));

        return response()->json([
            'message' => 'An OTP has been sent to your email.'
        ], 200);
    }

    // Step 2: Reset Password
    public function resetPassword(Request $request)
    {
        $ValidData = $request->validate([
            'email' => 'required|email|string',
            'token' => 'required',
            'password' => 'required'
        ]);

        // Find the user by email
        $user = Accounts::where('email', $ValidData['email'])->first();

         if (!$user) {
            return response()->json(['message' => 'No Record Found, Try another email']);
        }

         // Find the reset request by email
         $resetRequest = ForgetPassword::where('email', $user->email)->first();
         // Check if the reset request exists and if the token matches
        if (!$resetRequest || $resetRequest->token != $request->token) {
            return response()->json(['message' => 'Wrong OTP, Please try again']);
        }
        $user->fill([
            'password' =>$ValidData['password']
        ]);
        $user->save();

        // Delete the used token from the database
        $resetRequest->delete();

        return response()->json([
            'message' => 'Password reset successful',
        ]);



}
}
