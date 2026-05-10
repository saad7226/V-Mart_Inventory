<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Store;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Laravel\Socialite\Facades\Socialite;

class GoogleController extends Controller
{
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();

            // ── Case 1: User already linked by google_id ──────────────────────
            $existingByGoogleId = User::where('google_id', $googleUser->id)->first();
            if ($existingByGoogleId) {
                return $this->loginExistingUser($existingByGoogleId);
            }

            // ── Case 2: Account exists with same email (manual registration) ──
            $existingByEmail = User::where('email', $googleUser->email)->first();
            if ($existingByEmail) {
                // Link the google_id to the existing account
                $existingByEmail->update(['google_id' => $googleUser->id]);
                return $this->loginExistingUser($existingByEmail);
            }

            // ── Case 3: Brand new user — create account + store ───────────────
            DB::beginTransaction();

            $newUser = User::create([
                'name'                 => $googleUser->name,
                'email'               => $googleUser->email,
                'google_id'           => $googleUser->id,
                'password'            => bcrypt(uniqid('google_', true)), // random secure password
                'username'            => uniqid(),
                'is_google_registered' => true,
                // is_approved defaults to 0 — needs Super Admin approval
            ]);

            // Create a store for the new user (same as manual registration)
            $store = Store::create([
                'name'     => $googleUser->name . "'s Store",
                'owner_id' => $newUser->id,
            ]);

            $newUser->store_id = $store->id;
            $newUser->save();

            // Assign Admin role (store owner) — same as manual registration
            $newUser->assignRole('Admin');

            DB::commit();

            // Send welcome email (optional — wrapped so it won't break flow)
            try {
                $mailData = ['name' => $newUser->name, 'email' => $newUser->email];
                \Mail::to($newUser->email)->send(new \App\Mail\AccountCreatedMail($mailData));
            } catch (\Exception $mailEx) {
                // Email failure should not block registration
            }

            // New Google user needs approval — redirect to login with info message
            return redirect()->route('login')
                ->with('success', 'Welcome ' . $newUser->name . '! Your account has been created via Google. Your store "' . $store->name . '" is ready. Your account is pending Super Admin approval. You will be notified once approved.');

        } catch (Exception $e) {
            return redirect()->route('login')
                ->with('error', 'Google Sign-In failed: ' . $e->getMessage());
        }
    }

    /**
     * Log in an existing user with all the same checks as manual login.
     */
    private function loginExistingUser(User $user)
    {
        // Check if suspended
        if ($user->is_suspended == 1) {
            return redirect()->route('login')
                ->with('error', 'Your account has been temporarily suspended. Please contact the administrator.');
        }

        // Check if approved by Super Admin
        if (!$user->is_approved) {
            return redirect()->route('login')
                ->with('error', 'Your account is pending Super Admin approval. You will be notified by email once approved.');
        }

        // All checks passed — log the user in
        Auth::login($user);
        session()->regenerate();

        return redirect()->route('backend.admin.dashboard');
    }
}
