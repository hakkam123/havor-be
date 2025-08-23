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
    </style>
    @stack('head')
</head>
<body class="bg-slate-50 antialiased">
<div class="min-h-screen flex">

    <aside id="sidebar"
           class="hidden text-sm md:flex md:flex-col w-60 bg-white text-gray-700 transition-all duration-300">
        <div class="flex items-center justify-center h-20 border-b px-4">
            <img src="{{ asset('images/havor-besar.jpg') }}" alt="Havor Logo" class="sidebar-logo-big max-h-12 transition-all duration-300" />
            <img src="{{ asset('images/havor-kecil.jpg') }}" alt="Havor Logo" class="sidebar-logo-small max-h-12 transition-all duration-300" style="display:none;" />
        </div>
        <nav class="flex-1 px-2 py-4 text-sm">
            <ul class="space-y-1">
                <li>
                  <a href="{{ route('admin.dashboard') }}"
                     class="flex items-center gap-3 px-3 py-2 rounded-lg
                     @if(request()->routeIs('admin.dashboard')) bg-[#4783cc] text-white font-semibold @else hover:bg-slate-100 @endif">
                      <i class="bi bi-house-door-fill text-xl"></i>
                      <span class="sidebar-text truncate">Dashboard</span>
                  </a>
                </li>
                <!-- OFFERINGS GROUP -->
                <li class="mt-4 mb-1 px-4 text-xs text-slate-400 uppercase tracking-wider select-none sidebar-label">Offerings</li>
                <li>
                  <a href="{{ route('admin.services.index') }}"
                     class="flex items-center gap-3 px-3 py-2 rounded-lg
                     @if(request()->routeIs('admin.services.*')) bg-[#4783cc] text-white font-semibold @else hover:bg-slate-100 @endif">
                      <i class="bi bi-lightbulb-fill text-xl"></i>
                      <span class="sidebar-text truncate">Services</span>
                  </a>
                </li>
                <li>
                  <a href="{{ route('admin.products.index') }}"
                     class="flex items-center gap-3 px-3 py-2 rounded-lg
                     @if(request()->routeIs('admin.products.*')) bg-[#4783cc] text-white font-semibold @else hover:bg-slate-100 @endif">
                      <i class="bi bi-grid-1x2-fill text-xl"></i>
                      <span class="sidebar-text truncate">Products</span>
                  </a>
                </li>
                <!-- WORK GROUP -->
                <li class="mt-4 mb-1 px-4 text-xs text-slate-400 uppercase tracking-wider select-none sidebar-label">Work</li>
                <li>
                  <a href="{{ route('admin.clients.index') }}"
                     class="flex items-center gap-3 px-3 py-2 rounded-lg
                     @if(request()->routeIs('admin.clients.*')) bg-[#4783cc] text-white font-semibold @else hover:bg-slate-100 @endif">
                      <i class="bi bi-people-fill text-xl"></i>
                      <span class="sidebar-text truncate">Clients</span>
                  </a>
                </li>
                <li>
                  <a href="{{ route('admin.projects.index') }}"
                     class="flex items-center gap-3 px-3 py-2 rounded-lg
                     @if(request()->routeIs('admin.projects.*')) bg-[#4783cc] text-white font-semibold @else hover:bg-slate-100 @endif">
                      <i class="bi bi-briefcase-fill text-xl"></i>
                      <span class="sidebar-text truncate">Projects</span>
                  </a>
                </li>
                <li>
                  <a href="{{ route('admin.industries.index') }}"
                     class="flex items-center gap-3 px-3 py-2 rounded-lg
                     @if(request()->routeIs('admin.industries.*')) bg-[#4783cc] text-white font-semibold @else hover:bg-slate-100 @endif">
                      <i class="bi bi-building text-xl"></i>
                      <span class="sidebar-text truncate">Industries</span>
                  </a>
                </li>
                <!-- INSIGHTS GROUP -->
                <li class="mt-4 mb-1 px-4 text-xs text-slate-400 uppercase tracking-wider select-none sidebar-label">Insights & Resources</li>
                <li>
                  <a href="{{ route('admin.articles.index') }}"
                     class="flex items-center gap-3 px-3 py-2 rounded-lg
                     @if(request()->routeIs('admin.articles.*')) bg-[#4783cc] text-white font-semibold @else hover:bg-slate-100 @endif">
                      <i class="bi bi-bar-chart-line-fill text-xl"></i>
                      <span class="sidebar-text truncate">Articles</span>
                  </a>
                </li>
                <li>
                  <a href="{{ route('admin.leads.index') }}"
                     class="flex items-center gap-3 px-3 py-2 rounded-lg
                     @if(request()->routeIs('admin.leads.*')) bg-[#4783cc] text-white font-semibold @else hover:bg-slate-100 @endif">
                      <i class="bi bi-envelope-fill text-xl"></i>
                      <span class="sidebar-text truncate">Leads</span>
                  </a>
                </li>
                <!-- PAGES (DISABLED/PLACEHOLDER) -->
                <li class="mt-4 mb-1 px-4 text-xs text-slate-400 uppercase tracking-wider select-none sidebar-label">Pages</li>
                <li>
                  <a href="#" class="flex items-center gap-3 px-3 py-2 rounded-lg text-slate-400 bg-slate-100 cursor-not-allowed opacity-70 pointer-events-none">
                      <i class="bi bi-file-earmark-fill text-xl"></i>
                      <span class="sidebar-text truncate">Pages</span>
                  </a>
                </li>
            </ul>
        </nav>
        <div class="px-4 pb-6 border-t flex items-center sidebar-logout">
            <form method="POST" action="{{ route('admin.logout') }}" class="w-full">
                @csrf
                <button type="submit" class="w-full text-left px-4 py-2 text-sm hover:bg-slate-100 flex items-center gap-2">
                    <i class="bi bi-box-arrow-right me-2"></i>
                    <span class="sidebar-logout-text">Logout</span>
                </button>
            </form>
        </div>
    </aside>

    <!-- MOBILE SIDEBAR (tidak berubah dari sebelumnya, silakan sesuaikan jika perlu) -->
    <div class="md:hidden fixed top-0 left-0 right-0 z-30">
      <div class="flex items-center justify-between bg-white px-3 py-2 shadow">
          <div class="flex items-center gap-2">
              <button id="mobileOpenSidebar" class="p-2 rounded bg-slate-100">
                  <i class="bi bi-list"></i>
              </button>
              <span class="font-semibold">Havor Admin</span>
          </div>
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
      <aside class="absolute left-0 top-0 bottom-0 w-72 bg-white text-gray-700 p-4 overflow-auto">
          <div class="flex items-center justify-between mb-4">
              <div class="flex items-center gap-3">
                  <div class="w-10 h-10 rounded bg-slate-100 flex items-center justify-center">
                      <i class="bi bi-kanban-fill"></i>
                  </div>
                  <span class="font-semibold">Havor Admin</span>
              </div>
              <button id="mobileCloseSidebar" class="p-1 rounded bg-slate-100">
                  <i class="bi bi-x-lg"></i>
              </button>
          </div>
          <nav>
            <ul class="space-y-2 text-sm">
              <li><a href="{{ route('admin.dashboard') }}" class="block px-3 py-2 rounded hover:bg-slate-100">Dashboard</a></li>
              <li><a href="{{ route('admin.services.index') }}" class="block px-3 py-2 rounded hover:bg-slate-100">Services</a></li>
              <li><a href="{{ route('admin.products.index') }}" class="block px-3 py-2 rounded hover:bg-slate-100">Products</a></li>
              <li><a href="{{ route('admin.clients.index') }}" class="block px-3 py-2 rounded hover:bg-slate-100">Clients</a></li>
              <li><a href="{{ route('admin.projects.index') }}" class="block px-3 py-2 rounded hover:bg-slate-100">Projects</a></li>
              <li><a href="{{ route('admin.industries.index') }}" class="block px-3 py-2 rounded hover:bg-slate-100">Industries</a></li>
              <li><a href="{{ route('admin.articles.index') }}" class="block px-3 py-2 rounded hover:bg-slate-100">Articles</a></li>
              <li><a href="{{ route('admin.leads.index') }}" class="block px-3 py-2 rounded hover:bg-slate-100">Leads</a></li>
            </ul>
          </nav>
      </aside>
    </div>

    <div class="flex-1 min-h-screen flex flex-col">
        <header class="h-20 bg-white flex items-center px-4">
            <button id="sidebarToggle" class="p-2 rounded hover:bg-slate-100 mr-4">
                <i class="bi bi-list text-2xl text-slate-700"></i>
            </button>
            <div class="flex-1 flex items-center">
                <span class="text-xl font-bold text-slate-700">@yield('page-title','Dashboard')</span>
            </div>
            <div class="flex items-center gap-4">
                <div x-data="{ open: false }" class="relative">
                    <button @click="open = !open" class="flex items-center gap-2 px-3 py-2 rounded hover:bg-slate-50 text-slate-700">
                        <i class="bi bi-person-circle text-2xl"></i>
                        <span class="hidden sm:inline">{{ auth()->user()->name }}</span>
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
    sidebar.classList.toggle('w-60');
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