<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            return redirect()->intended(route('admin.dashboard'));
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->withInput($request->only('email'));
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect()->route('admin.login');
    }
}
