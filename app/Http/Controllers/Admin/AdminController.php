<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Models\Service;
use App\Models\Project;
use App\Models\Article;
use App\Models\Lead;
use App\Models\Product;
use App\Models\Industry;
use App\Models\HomepageFeature;

class AdminController extends Controller
{
    public function dashboard()
    {
        $stats = [
            'services' => Service::count(),
            'projects' => Project::count(),
            'articles' => Article::count(),
            'leads' => Lead::count(),
            'products' => Product::count(),
            'industries' => Industry::count(),
            'homepage_features' => HomepageFeature::count(),
        ];

        $recent_leads = Lead::latest()->take(5)->get();
        $recent_articles = Article::latest()->take(5)->get();

        return view('admin.dashboard', compact('stats', 'recent_leads', 'recent_articles'));
    }

    public function login()
    {
        if (Auth::check()) {
            return redirect()->route('admin.dashboard');
        }
        return view('admin.login');
    }

    public function authenticate(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        try {
            if (!$token = JWTAuth::attempt($credentials)) {
                return back()->withErrors([
                    'email' => 'The provided credentials do not match our records.',
                ])->withInput($request->only('email'));
            }

            $user = JWTAuth::user();
            
            $remember = $request->has('remember');
            $ttl = $remember ? 43200 : 120; // 30 days vs 2 hours

            session(['admin_token' => $token]);
            session(['admin_user_id' => $user->id]);
            session(['login_time' => now()]);
            
            $cookie = cookie('admin_token', $token, $ttl, null, null, false, true);
            
            $sessionData = session()->all();
            $request->session()->regenerate();
            
            foreach ($sessionData as $key => $value) {
                session([$key => $value]);
            }

            return redirect()->intended(route('admin.dashboard'))
                ->with('success', 'Welcome back!')
                ->cookie($cookie);

        } catch (\Exception $e) {
            Log::error('Admin login error', [
                'error' => $e->getMessage(),
                'email' => $request->email
            ]);
            
            return back()->withErrors([
                'email' => 'Login failed. Please try again.',
            ])->withInput($request->only('email'));
        }
    }

    public function logout(Request $request)
    {
        try {

            $token = session('admin_token') ?? $request->cookie('admin_token');
            if ($token) {
                JWTAuth::setToken($token)->invalidate();
                Log::info('Admin JWT token invalidated successfully');
            }
        } catch (\Exception $e) {
            Log::warning('Admin logout token invalidation failed', ['error' => $e->getMessage()]);
        }


        session()->forget('admin_token');
        cookie()->queue(cookie()->forget('admin_token'));
        

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        Log::info('Admin logged out successfully');
        return redirect()->route('admin.login')->with('success', 'Logged out successfully');
    }
}
