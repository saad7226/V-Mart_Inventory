<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

/**
 * AuthApiController
 *
 * Handles authentication for both:
 *   - Android Seller App: POST /api/login → receives Bearer token
 *   - Logout:             POST /api/logout (token revoked)
 *
 * Token format used by Android (Retrofit/OkHttp):
 *   Header: Authorization: Bearer <token>
 */
class AuthApiController extends Controller
{
    /**
     * POST /api/login
     *
     * Validates seller credentials, checks account status (suspended / not approved),
     * issues a Sanctum personal access token.
     *
     * Android Request Body (JSON):
     * {
     *   "email": "seller@vmart.com",
     *   "password": "secret123",
     *   "device_name": "Samsung Galaxy S22"   // optional, for token label
     * }
     *
     * Success Response (200):
     * {
     *   "success": true,
     *   "token": "3|abc123...",
     *   "token_type": "Bearer",
     *   "user": { "id": 1, "name": "...", "email": "...", "roles": [...] }
     * }
     */
    public function login(Request $request)
    {
        try {
            $request->validate([
                'email' => 'required|email',
                'password' => 'required|string|min:6',
            ]);

            // Find user by email
            $user = User::where('email', $request->email)->first();

            // Check credentials
            if (!$user || !Hash::check($request->password, $user->password)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid email or password.',
                ], 401);
            }

            // Check account suspension
            if ($user->is_suspended) {
                return response()->json([
                    'success' => false,
                    'message' => 'Your account has been suspended. Contact support.',
                ], 403);
            }

            // Check Super Admin approval
            if (!$user->is_approved) {
                return response()->json([
                    'success' => false,
                    'message' => 'Your account is pending Super Admin approval.',
                ], 403);
            }

            // Revoke all previous tokens for this device (prevents token accumulation)
            $deviceName = $request->device_name ?? 'android-seller-app';
            $user->tokens()->where('name', $deviceName)->delete();

            // Issue a fresh Sanctum personal access token
            $token = $user->createToken($deviceName)->plainTextToken;

            return response()->json([
                'success' => true,
                'token' => $token,
                'token_type' => 'Bearer',
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'store_id' => $user->store_id,
                    'roles' => $user->getRoleNames(), // Spatie roles
                    'pro_pic' => $user->pro_pic,
                ],
            ], 200);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Login failed. Please try again.',
                'error' => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }

    /**
     * POST /api/logout  (requires: Authorization: Bearer <token>)
     *
     * Revokes the current token. The seller is logged out from the Android app.
     *
     * Success Response (200):
     * { "success": true, "message": "Logged out successfully." }
     */
    public function logout(Request $request)
    {
        try {
            // Delete only the current token (not all tokens — allows multi-device)
            $request->user()->currentAccessToken()->delete();

            return response()->json([
                'success' => true,
                'message' => 'Logged out successfully.',
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Logout failed.',
                'error' => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }

    /**
     * GET /api/me  (requires: Authorization: Bearer <token>)
     *
     * Returns the authenticated seller's profile.
     * Useful for Android app to refresh user data after startup.
     *
     * Success Response (200):
     * { "success": true, "data": { "id": 1, "name": "...", ... } }
     */
    public function me(Request $request)
    {
        $user = $request->user();

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'store_id' => $user->store_id,
                'roles' => $user->getRoleNames(),
                'pro_pic' => $user->pro_pic,
            ],
        ], 200);
    }
}
