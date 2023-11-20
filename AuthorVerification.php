<?php

namespace App\Http\Controllers;

use App\Models\Authors;

class AuthorVerification extends Controller
{
    public function verify($token)
    {
        // Find the user by the verification token
        $user = Authors::where('verification_token', $token)->first();

        if ($user) {
            // Mark the user as verified
            $user->account_verified = 1;
            $user->verification_token = null; // Optionally, clear the verification token
            $user->save();

            return view('success'); // for success case
        }

        return view('error'); // for error case
    }
}
