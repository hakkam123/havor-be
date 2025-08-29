<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class AuthController extends Controller
{
    /**
     * Handle admin login (for web admin)
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        // Check if this is API request or web request
        if ($request->expectsJson()) {
            // API Login - return JSON response
            if (!$token = JWTAuth::attempt($credentials)) {
                return response()->json(['error' => 'Invalid credentials'], 401);
            }

            return response()->json([
                'token' => $token,
                'user' => Auth::user()
            ]);
        } else {
            // Web Admin Login - redirect with session/cookie
            if (!$token = JWTAuth::attempt($credentials)) {
                return back()->withErrors([
                    'email' => 'Invalid credentials'
                ])->withInput();
            }

            // Set token expiration based on "Remember Me"
            $ttl = $request->has('remember') ? 43200 : 60; // 30 days vs 1 hour
            JWTAuth::factory()->setTTL($ttl);
            
            // Regenerate token with new TTL
            $token = JWTAuth::fromUser(Auth::user());

            // Store token based on remember me
            if ($request->has('remember')) {
                // Store in cookie for 30 days
                cookie()->queue('admin_token', $token, 43200);
            } else {
                // Store in session
                session(['admin_token' => $token]);
            }

            return redirect()->route('admin.dashboard')->with('success', 'Welcome back!');
        }
    }

    /**
     * Handle admin logout
     */
    public function logout(Request $request)
    {
        if ($request->expectsJson()) {
            // API Logout
            try {
                JWTAuth::invalidate(JWTAuth::getToken());
                return response()->json(['message' => 'Logged out successfully']);
            } catch (JWTException $e) {
                return response()->json(['error' => 'Failed to logout'], 500);
            }
        } else {
            // Web Admin Logout
            try {
                $token = session('admin_token') ?? $request->cookie('admin_token');
                if ($token) {
                    JWTAuth::setToken($token)->invalidate();
                }
            } catch (JWTException $e) {
                // Token already invalid or not found
            }

            // Clear session and cookie
            session()->forget('admin_token');
            cookie()->queue(cookie()->forget('admin_token'));

            return redirect()->route('admin.login')->with('success', 'Logged out successfully');
        }
    }

    /**
     * Get authenticated user info
     */
    public function user(Request $request)
    {
        try {
            if (!$user = JWTAuth::parseToken()->authenticate()) {
                return response()->json(['error' => 'User not found'], 404);
            }
        } catch (JWTException $e) {
            return response()->json(['error' => 'Token invalid'], 401);
        }

        return response()->json($user);
    }
}
