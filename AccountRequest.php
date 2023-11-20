<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Authors;

class AccountRequest extends Controller
{
    public function request(Request $request)
    {
        try {
            if ($request->has('email')) {
                $email = $request->input('email');

                $user = Authors::select('author_id', 'name', 'email', 'about_me', 'profile_picture_path')
                    ->where('email', $email)
                    ->first();

                if ($user) {
                    return response()->json($user);
                } else {
                    return response()->json(['message' => 'User not found'], 404);
                }
            } else {
                return response()->json(['message' => 'Email parameter is missing'], 400);
            }
        } catch (\Exception $e) {
            return response()->json(['message' => 'Database error: ' . $e->getMessage()], 500);
        }
    }
}
