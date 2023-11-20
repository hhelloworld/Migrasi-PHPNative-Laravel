<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Authors;
use Illuminate\Support\Facades\Mail;
use App\Mail\VerificationMail;


class AuthorRegister extends Controller
{
    public function register(Request $request)
    {
        $name = $request->input('name');
        $email = $request->input('email');
        $password = $request->input('password');

        $verificationToken = bin2hex(random_bytes(32));

        try {
            $user = Authors::where('email', $email)->first();

            if ($user) {
                if ($user->account_verified == 0) {
                    $user->verification_token = $verificationToken;
                    $user->save();

                    // Your email sending logic goes here
                    $this->sendVerificationEmail($email, $name, $verificationToken);

                    $response = [
                        'status' => 'success',
                        'message' => 'Verification email has been resent. Please check your email for verification.',
                    ];
                } else {
                    $response = [
                        'status' => 'error',
                        'message' => 'Email already exists, and the account is verified.',
                    ];
                }
            } else {
                $hashedPassword = bcrypt($password);

                $newUser = new Authors();
                $newUser->name = $name;
                $newUser->email = $email;
                $newUser->password = $hashedPassword;
                $newUser->verification_token = $verificationToken;
                $newUser->account_verified = 0;
                $newUser->save();

                // Your email sending logic goes here
                $this->sendVerificationEmail($email, $name, $verificationToken);

                $response = [
                    'status' => 'success',
                    'message' => 'Registration successful! Please check your email for verification.',
                ];
            }
        } catch (\Exception $e) {
            $response = [
                'status' => 'error',
                'message' => 'An error occurred: ' . $e->getMessage(),
            ];
        }

        return response()->json($response);
    }

    private function sendVerificationEmail($email, $name, $verificationToken)
    {
        // Your email sending logic goes here
        // Use Laravel's mail functionality to send the verification email
        // Example: Mail::to($email)->send(new VerificationMail($name, $verificationToken));

        Mail::to($email)->send(new VerificationMail($name, $verificationToken));
    }
}
