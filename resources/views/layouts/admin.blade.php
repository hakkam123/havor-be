<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <title>@yield('title', 'Havor Admin')</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="icon" type="image/x-icon" href="{{ asset('images/havor-kecil.jpg') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <style>
        #sidebar.w-20 .sidebar-logo-big { display: none !important; }
        #sidebar.w-20 .sidebar-logo-small { display: block !important; }
        #sidebar .sidebar-logo-big { display: block; }
        #sidebar .sidebar-logo-small { display: none; }
        #sidebar.w-20 .sidebar-label,
        #sidebar.w-20 .sidebar-text,
        #sidebar.w-20 .sidebar-logout-text {
            display: none !important;
        }
        html, body { height: 100%; }
        main { min-height: 100vh; }
        .card-shadow { box-shadow: 0 6px 18px rgba(15, 23, 42, 0.08); }
        [x-cloak] { display: none !important; }
        
        /* Custom Sidebar Blue Gradient */
        .sidebar-gradient {
            background: linear-gradient(180deg, #1e3a8a 0%, #1e40af 25%, #2563eb 50%, #3b82f6 75%, #60a5fa 100%);
        }
        
        /* Active menu item style */
        .menu-active {
            background: rgba(255, 255, 255, 0.2);
            border-radius: 25px;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        /* Menu hover effect */
        .menu-item:hover {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 25px;
            transform: translateX(4px);
            transition: all 0.2s ease;
        }
        
        /* Section labels styling */
        .section-label {
            color: rgba(255, 255, 255, 0.6);
            font-size: 11px;
            font-weight: 600;
            letter-spacing: 1px;
            text-transform: uppercase;
            margin: 20px 0 8px 0;
        }
    </style>
    @stack('head')
</head>
<body class="bg-slate-50 antialiased">
<div class="min-h-screen flex">

    <aside id="sidebar"
           class="hidden text-sm md:flex md:flex-col w-100 sidebar-gradient text-white transition-all duration-300">
        
        <!-- Navigation -->
        <nav class="flex-1 px-6 py-2 text-sm">
            <!-- HOME Section -->
            <div class="section-label sidebar-label">HOME</div>
            <ul class="space-y-1">
                <li>
                    <a href="{{ route('admin.dashboard') }}"
                       class="flex items-center gap-3 px-4 py-3 rounded-full menu-item
                       @if(request()->routeIs('admin.dashboard')) menu-active menu-new @endif">
                        <i class="bi bi-house-door-fill text-lg"></i>
                        <span class="sidebar-text">Dashboard</span>
                    </a>
                </li>
            </ul>

            <!-- BUSINESS Section -->
            <div class="section-label sidebar-label">BUSINESS</div>
            <ul class="space-y-1">
                <li>
                    <a href="{{ route('admin.services.index') }}"
                       class="flex items-center gap-3 px-4 py-3 rounded-full menu-item
                       @if(request()->routeIs('admin.services.*')) menu-active @endif">
                        <i class="bi bi-lightbulb-fill text-lg"></i>
                        <span class="sidebar-text">Services</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.products.index') }}"
                       class="flex items-center gap-3 px-4 py-3 rounded-full menu-item
                       @if(request()->routeIs('admin.products.*')) menu-active @endif">
                        <i class="bi bi-box-seam text-lg"></i>
                        <span class="sidebar-text">Products</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.clients.index') }}"
                       class="flex items-center gap-3 px-4 py-3 rounded-full menu-item
                       @if(request()->routeIs('admin.clients.*')) menu-active @endif">
                        <i class="bi bi-shop text-lg"></i>
                        <span class="sidebar-text">Clients</span>
                    </a>
                </li>
            </ul>

            <!-- WORK Section -->
            <div class="section-label sidebar-label">WORK</div>
            <ul class="space-y-1">
                <li>
                    <a href="{{ route('admin.projects.index') }}"
                       class="flex items-center gap-3 px-4 py-3 rounded-full menu-item
                       @if(request()->routeIs('admin.projects.*')) menu-active @endif">
                        <i class="bi bi-briefcase-fill text-lg"></i>
                        <span class="sidebar-text">Projects</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.industries.index') }}"
                       class="flex items-center gap-3 px-4 py-3 rounded-full menu-item
                       @if(request()->routeIs('admin.industries.*')) menu-active @endif">
                        <i class="bi bi-building text-lg"></i>
                        <span class="sidebar-text">Industries</span>
                    </a>
                </li>
            </ul>

            <!-- COMMUNICATION Section -->
            <div class="section-label sidebar-label">COMMUNICATION</div>
            <ul class="space-y-1">
                <li>
                    <a href="{{ route('admin.leads.index') }}"
                       class="flex items-center gap-3 px-4 py-3 rounded-full menu-item
                       @if(request()->routeIs('admin.leads.*')) menu-active @endif">
                        <i class="bi bi-chat-dots-fill text-lg"></i>
                        <span class="sidebar-text">Messages</span>
                    </a>
                </li>
            </ul>

            <!-- ANALYTICS Section -->
            <div class="section-label sidebar-label">ANALYTICS</div>
            <ul class="space-y-1">
                <li>
                    <a href="{{ route('admin.articles.index') }}"
                       class="flex items-center gap-3 px-4 py-3 rounded-full menu-item
                       @if(request()->routeIs('admin.articles.*')) menu-active @endif">
                        <i class="bi bi-bar-chart-line-fill text-lg"></i>
                        <span class="sidebar-text">Reports</span>
                    </a>
                </li>
            </ul>
        </nav>
    </aside>

    <!-- Mobile Sidebar -->
    <div class="md:hidden fixed top-0 left-0 right-0 z-30">
        <div class="flex items-center justify-between bg-white px-3 py-2 shadow">
            <div>
                <div class="inline-flex items-center gap-2 text-sm text-slate-700">
                    <i class="bi bi-person-circle"></i>
                    <span>{{ auth()->user()->name }}</span>
                </div>
            </div>
        </div>
    </div>

    <div id="mobileSidebar" class="fixed inset-0 z-40 hidden">
        <div class="absolute inset-0 bg-black/50" id="mobileSidebarBackdrop"></div>
        <aside class="absolute left-0 top-0 bottom-0 w-72 sidebar-gradient text-white p-4 overflow-auto">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center gap-3">
                    <span class="font-bold text-xl">SMART</span>
                </div>
                <button id="mobileCloseSidebar" class="p-1 rounded bg-white/20">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>
            <nav>
                <div class="section-label">HOME</div>
                <ul class="space-y-2 text-sm mb-4">
                    <li><a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-3 py-2 rounded-full menu-item"><i class="bi bi-house-door-fill"></i> Dashboard</a></li>
                </ul>
                
                <div class="section-label">BUSINESS</div>
                <ul class="space-y-2 text-sm mb-4">
                    <li><a href="{{ route('admin.services.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-full menu-item"><i class="bi bi-lightbulb-fill"></i> Services</a></li>
                    <li><a href="{{ route('admin.products.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-full menu-item"><i class="bi bi-box-seam"></i> Products</a></li>
                    <li><a href="{{ route('admin.clients.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-full menu-item"><i class="bi bi-shop"></i> Clients</a></li>
                </ul>
                
                <div class="section-label">WORK</div>
                <ul class="space-y-2 text-sm mb-4">
                    <li><a href="{{ route('admin.projects.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-full menu-item"><i class="bi bi-briefcase-fill"></i> Projects</a></li>
                    <li><a href="{{ route('admin.industries.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-full menu-item"><i class="bi bi-building"></i> Industries</a></li>
                </ul>
                
                <div class="section-label">COMMUNICATION</div>
                <ul class="space-y-2 text-sm mb-4">
                    <li><a href="{{ route('admin.leads.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-full menu-item"><i class="bi bi-chat-dots-fill"></i> Messages</a></li>
                </ul>
                
                <div class="section-label">ANALYTICS</div>
                <ul class="space-y-2 text-sm">
                    <li><a href="{{ route('admin.articles.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-full menu-item"><i class="bi bi-bar-chart-line-fill"></i> Reports</a></li>
                </ul>
            </nav>
        </aside>
    </div>

    <!-- Main Content Area -->
    <div class="flex-1 min-h-screen flex flex-col">
        <header class="h-20 bg-white flex items-center px-4 shadow-sm">
            <button id="sidebarToggle" class="p-2 rounded hover:bg-slate-100 mr-4">
                <i class="bi bi-list text-2xl text-slate-700"></i>
            </button>
            <div class="flex-1 flex items-center">
                <span class="text-md font-bold text-slate-700">@yield('page-title','Dashboard')</span>
            </div>
            <div class="flex items-center gap-4">
                <div x-data="{ open: false }" class="relative">
                    <button @click="open = !open" class="flex items-center gap-2 px-3 py-2 rounded hover:bg-slate-50 text-slate-700">
                        <i class="bi bi-person-circle text-md"></i>
                        <span class="hidden sm:inline text-sm">{{ auth()->user()->name }}</span>
                        <i class="bi bi-chevron-down"></i>
                    </button>
                    <div x-show="open" @click.away="open = false" x-cloak
                         x-transition
                         class="absolute right-0 mt-2 w-48 bg-white rounded shadow-lg z-50 border border-slate-100">
                        <form method="POST" action="{{ route('admin.logout') }}">
                            @csrf
                            <button type="submit" class="w-full text-left px-4 py-2 text-sm hover:bg-slate-100">
                                <i class="bi bi-box-arrow-right me-2"></i> Logout
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </header>

        <main class="flex-1 p-6 max-w-7xl mx-auto w-full">
            @if(session('success'))
                <div class="mb-4">
                    <div class="rounded-md bg-emerald-50 p-3 text-emerald-800">{{ session('success') }}</div>
                </div>
            @endif

            @if(session('error'))
                <div class="mb-4">
                    <div class="rounded-md bg-red-50 p-3 text-red-700">{{ session('error') }}</div>
                </div>
            @endif

            @if($errors->any())
                <div class="mb-4">
                    <div class="rounded-md bg-red-50 p-3 text-red-700">
                        <ul class="list-disc pl-5">
                            @foreach($errors->all() as $err)
                                <li>{{ $err }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif

            @yield('content')
        </main>
    </div>
</div>

<script>
const sidebarToggle = document.getElementById('sidebarToggle');
const sidebar = document.getElementById('sidebar');
sidebarToggle?.addEventListener('click', () => {
    sidebar.classList.toggle('w-20');
    sidebar.classList.toggle('w-100');
    sidebar.classList.toggle('collapsed');
});

const mobileOpenBtn = document.getElementById('mobileOpenSidebar');
const mobileSidebar = document.getElementById('mobileSidebar');
const mobileBackdrop = document.getElementById('mobileSidebarBackdrop');
const mobileClose = document.getElementById('mobileCloseSidebar');

mobileOpenBtn?.addEventListener('click', () => mobileSidebar.classList.remove('hidden'));
mobileBackdrop?.addEventListener('click', () => mobileSidebar.classList.add('hidden'));
mobileClose?.addEventListener('click', () => mobileSidebar.classList.add('hidden'));
</script>

@stack('scripts')
</body>
</html>