<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;

class NewPasswordController extends Controller
{
    // Show the "Create New Password" form (from the email link)
    public function create(Request $request)
    {
        return view('auth.reset-password', ['request' => $request]);
    }

    // Handle the actual password update
    public function store(Request $request)
    {
        $request->validate([
            'token' => ['required'],
            'email' => ['required', 'email'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        // This logic checks the token against your password_reset_tokens table
        $status = Password::broker()->reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password), // Ensure this is hashed [cite: 2025-12-04]
                    'remember_token' => Str::random(60),
                ])->save();

                event(new PasswordReset($user));
        }
);

        return $status == Password::PASSWORD_RESET
            ? redirect()->route('login')->with('status', __($status))
            : back()->withInput($request->only('email'))
                   ->withErrors(['email' => __($status)]);
    }
}
