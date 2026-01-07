<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(Request $request): RedirectResponse
    {
        // 1. Validate the input fields
        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name'  => ['required', 'string', 'max:255'],
            'middle_name'=> ['nullable', 'string', 'max:255'],
            'extension'  => ['nullable', 'string', 'max:10'],
            'email'      => ['required', 'email', 'max:255', 'unique:users,email,' . $request->user()->id],
        ]);

        // 2. Update the user model with validated data
        $request->user()->fill($validated);

        // 3. Reset email verification if email changed (Optional safety check)
        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        // 4. Save changes
        $request->user()->save();

        // 5. Redirect back with success message
        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        // 1. Validate password before deletion for security
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        // 2. Logout the user
        Auth::logout();

        // 3. Delete the user record
        $user->delete();

        // 4. Invalidate session
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
