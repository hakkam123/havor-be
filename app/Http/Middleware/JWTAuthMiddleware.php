<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Illuminate\Support\Facades\Log;

class JWTAuthMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        try {
            // Get token from session or cookie
            $token = session('admin_token') ?? $request->cookie('admin_token');
            
            if (!$token) {
                throw new JWTException('Token not provided');
            }

            // Set token and authenticate
            JWTAuth::setToken($token);
            $user = JWTAuth::authenticate();

            if (!$user) {
                throw new JWTException('User not found');
            }

            // Set authenticated user for this request
            Auth::login($user);

        } catch (JWTException $e) {
            Log::warning('JWT Authentication failed', [
                'error' => $e->getMessage(),
                'url' => $request->url()
            ]);
            
            // Clear invalid tokens
            session()->forget('admin_token');
            cookie()->queue(cookie()->forget('admin_token'));
            
            return redirect()->route('admin.login')
                ->withErrors(['error' => 'Please login to continue']);
                
        } catch (\Exception $e) {
            Log::error('Authentication error', [
                'error' => $e->getMessage(),
                'url' => $request->url()
            ]);
            
            return redirect()->route('admin.login')
                ->withErrors(['error' => 'Authentication failed. Please try again']);
        }

        return $next($request);
    }
}