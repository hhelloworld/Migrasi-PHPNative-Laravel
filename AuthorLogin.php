<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AuthorAuth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth; // Add this line

class AuthorLogin extends Controller
{
    public function login(Request $request)
    {
        $author = AuthorAuth::where('email', $request->email)->first();

        if (!$author || !Hash::check($request->password, $author->password)) {
            return response()->json(['error' => 'The provided credentials are incorrect.'], 401);
        }

        if ($author->account_verified === 0) {
            return response()->json(['error' => 'Account not verified.'], 401);
        }

        // Log the user in
        Auth::login($author); // Add this line
        \Log::info('Login successful' . $request->email);

        // If all checks pass
        return response()->json(['status' => 'success'], 200);

    }
}
