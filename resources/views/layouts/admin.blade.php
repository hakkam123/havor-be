<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Havor Admin')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <style>
        body {
            display: flex;
        }

        .sidebar {
            width: 300px;
            min-height: 100vh;
            transition: all 0.3s ease;
            background-color: #f8f9fa;
        }

        .sidebar.collapsed {
            width: 80px;
        }

        .sidebar .nav-link {
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            transition: all 0.2s ease;
        }

        .sidebar.collapsed .nav-link span {
            display: none;
        }

        .sidebar.collapsed .nav-item i {
            margin-right: 0;
        }

        .sidebar .nav-item i {
            margin-right: 10px;
        }

        .nav-link.active {
            background-color: #0d6efd;
            color: white !important;
        }

        main {
            flex-grow: 1;
            padding: 0;
        }

        .dropdown-toggle::after {
            float: right;
            margin-top: 7px;
        }

        .top-navbar {
            padding: 10px 20px;
            background-color: #fff;
            border-bottom: 1px solid #dee2e6;
        }

        .main-wrapper {
            width: 100%;
        }
    </style>
</head>
<body>
    <nav class="sidebar bg-light p-3" id="sidebar">
        <div class="d-flex justify-content-between align-items-center mb-5">
            <span class="fw-bold fs-5">Havor Admin</span>
        </div>

        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link @if(request()->routeIs('admin.dashboard')) active @endif" href="{{ route('admin.dashboard') }}">
                    <i class="bi bi-house-door"></i> <span>Dashboard</span>
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link @if(request()->routeIs('admin.homepage-features.index')) active @endif" href="{{ route('admin.homepage-features.index') }}">
                    <i class="bi bi-house"></i> <span>Home</span>
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link"
                data-bs-toggle="collapse"
                href="#offeringsSubmenu"
                role="button"
                aria-expanded="@if(request()->routeIs('admin.services.*') || request()->routeIs('admin.products.*')) true @else false @endif"
                aria-controls="offeringsSubmenu">
                    <i class="bi bi-lightbulb"></i> <span>Offerings</span>
                </a>
                <div class="collapse @if(request()->routeIs('admin.services.*') || request()->routeIs('admin.products.*')) show @endif" id="offeringsSubmenu">
                    <ul class="nav flex-column ms-3">
                        <li class="nav-item">
                            <a class="nav-link @if(request()->routeIs('admin.services.*')) active @endif" href="{{ route('admin.services.index') }}">Services</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link @if(request()->routeIs('admin.products.*')) active @endif" href="{{ route('admin.products.index') }}">Products</a>
                        </li>
                    </ul>
                </div>
            </li>

            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="collapse" href="#workSubmenu" role="button"
                aria-expanded="@if(request()->routeIs('admin.projects.*') || request()->routeIs('admin.industries.*') || request()->routeIs('admin.clients.*')) true @else false @endif"
                aria-controls="workSubmenu">
                    <i class="bi bi-briefcase"></i> <span>Work</span>
                </a>
                <div class="collapse @if(request()->routeIs('admin.projects.*') || request()->routeIs('admin.industries.*') || request()->routeIs('admin.clients.*')) show @endif" id="workSubmenu">
                    <ul class="nav flex-column ms-3">
                        <li class="nav-item">
                            <a class="nav-link @if(request()->routeIs('admin.clients.*')) active @endif"
                            href="{{ route('admin.clients.index') }}">Clients</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link @if(request()->routeIs('admin.projects.*')) active @endif"
                            href="{{ route('admin.projects.index') }}">Projects</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link @if(request()->routeIs('admin.industries.*')) active @endif"
                            href="{{ route('admin.industries.index') }}">Industries</a>
                        </li>
                    </ul>
                </div>
            </li>


            <li class="nav-item">
                <a class="nav-link"
                data-bs-toggle="collapse"
                href="#insightsSubmenu"
                role="button"
                aria-expanded="@if(request()->routeIs('admin.articles.*') || request()->routeIs('admin.leads.*')) true @else false @endif"
                aria-controls="insightsSubmenu">
                    <i class="bi bi-bar-chart"></i> <span>Insights & Resources</span>
                </a>
                <div class="collapse @if(request()->routeIs('admin.articles.*') || request()->routeIs('admin.leads.*')) show @endif" id="insightsSubmenu">
                    <ul class="nav flex-column ms-3">
                        <li class="nav-item">
                            <a class="nav-link @if(request()->routeIs('admin.articles.*')) active @endif" href="{{ route('admin.articles.index') }}">
                                Articles
                            </a>
                            <a class="nav-link @if(request()->routeIs('admin.leads.*')) active @endif" href="{{ route('admin.leads.index') }}">
                                Leads
                            </a>
                        </li>
                    </ul>
                </div>
            </li>

            <li class="nav-item">
                <a class="nav-link" href="javascript:void(0);">
                    <i class="bi bi-info-circle"></i> <span>About</span>
                </a>
            </li>
        </ul>
    </nav>

    <div class="main-wrapper">
        <nav class="top-navbar navbar navbar-expand-lg navbar-light">
            <div class="container-fluid">
                <button class="btn btn-sm btn-outline-secondary" id="sidebarToggle">
                    <i class="bi bi-chevron-left"></i>
                </button>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#topNavbar" aria-controls="topNavbar" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse justify-content-end" id="topNavbar">
                    <ul class="navbar-nav">
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                                <i class="bi bi-person-circle"></i> {{ auth()->user()->name }}
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li>
                                    <form action="{{ route('admin.logout') }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="dropdown-item">
                                            <i class="bi bi-box-arrow-right"></i> Logout
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>

        <main class="p-4">
            <div class="d-flex justify-content-between align-items-center pb-3 border-bottom">
                {{-- <h1 class="h4">@yield('page-title', 'Dashboard')</h1> --}}
                @yield('page-actions')
            </div>

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-triangle"></i> {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @yield('content')
        </main>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.getElementById('sidebarToggle').addEventListener('click', function () {
            document.getElementById('sidebar').classList.toggle('collapsed');
        });
    </script>
    @stack('scripts')
</body>
</html>
