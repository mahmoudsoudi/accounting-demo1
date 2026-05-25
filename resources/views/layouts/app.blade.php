<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ in_array(app()->getLocale(), config('locales.rtl', [])) ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', __('Dashboard')) - {{ __(config('app.name')) }}</title>
    @if(in_array(app()->getLocale(), config('locales.rtl', [])))
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    @else
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    @endif
    <link href="https://code.jquery.com/ui/1.14.1/themes/base/jquery-ui.min.css" rel="stylesheet">
    <style>
        body { background: #f4f6f9; }
        .sidebar { min-height: calc(100vh - 56px); background: #1e293b; }
        .sidebar .nav-link { color: #cbd5e1; border-radius: .375rem; margin-bottom: .25rem; }
        .sidebar .nav-link:hover, .sidebar .nav-link.active { background: #334155; color: #fff; }
        .sidebar-toggle { display: flex; align-items: center; justify-content: space-between; }
        .sidebar-chevron { font-size: 0.75rem; opacity: 0.7; transition: transform 0.2s; }
        .sidebar-toggle[aria-expanded="true"] .sidebar-chevron { transform: rotate(90deg); }
        .sidebar-submenu { padding-left: 0.5rem; margin-bottom: 0.25rem; border-left: 1px solid #334155; margin-left: 0.75rem; }
        [dir="rtl"] .sidebar-submenu { padding-left: 0; padding-right: 0.5rem; border-left: none; border-right: 1px solid #334155; margin-left: 0; margin-right: 0.75rem; }
        .sidebar-submenu .nav-link { font-size: 0.8125rem; padding: 0.3rem 0.65rem; color: #94a3b8; margin-bottom: 0.125rem; }
        .sidebar-submenu .nav-link:hover, .sidebar-submenu .nav-link.active { background: #334155; color: #fff; }
        .offcanvas.sidebar { background: #1e293b; }
        .offcanvas.sidebar .offcanvas-header { border-color: #334155 !important; }
        .navbar-toggler { border-color: rgba(255, 255, 255, 0.25); }
        .metric-card { border-left: 4px solid #0d6efd; }
        .metric-card.success { border-left-color: #198754; }
        .metric-card.warning { border-left-color: #ffc107; }
        .metric-card.danger { border-left-color: #dc3545; }
    </style>
    @stack('styles')
</head>
<body>
    <nav class="navbar navbar-dark bg-dark">
        <div class="container-fluid">
            @auth
            <button class="navbar-toggler d-md-none me-2" type="button"
                    data-bs-toggle="offcanvas" data-bs-target="#sidebarOffcanvas"
                    aria-controls="sidebarOffcanvas" aria-label="{{ __('Toggle navigation') }}">
                <span class="navbar-toggler-icon"></span>
            </button>
            @endauth
            <a class="navbar-brand" href="{{ route('dashboard') }}">{{ __(config('app.name')) }}</a>
            <div class="d-flex align-items-center gap-2 gap-md-3 ms-auto">
                <div class="btn-group btn-group-sm">
                    @foreach(config('locales.supported', ['en', 'fr', 'ar']) as $locale)
                        <a href="{{ route('locale.switch', $locale) }}"
                           class="btn btn-outline-light {{ app()->getLocale() === $locale ? 'active' : '' }}"
                           title="{{ config('locales.labels.'.$locale, strtoupper($locale)) }}">
                            {{ strtoupper($locale) }}
                        </a>
                    @endforeach
                </div>
                @auth
                    <span class="text-light small d-none d-sm-inline">{{ auth()->user()->name }} ({{ auth()->user()->role ? __(auth()->user()->role->name) : '—' }})</span>
                    <form action="{{ route('logout') }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-outline-light btn-sm">{{ __('Logout') }}</button>
                    </form>
                @endauth
            </div>
        </div>
    </nav>

    <div class="container-fluid">
        <div class="row">
            @auth
            <div class="offcanvas offcanvas-start sidebar d-md-none" tabindex="-1" id="sidebarOffcanvas" aria-labelledby="sidebarOffcanvasLabel">
                <div class="offcanvas-header">
                    <h5 class="offcanvas-title text-light" id="sidebarOffcanvasLabel">{{ __('Menu') }}</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" aria-label="{{ __('Close') }}"></button>
                </div>
                <div class="offcanvas-body p-3 pt-0">
                    @include('partials.sidebar', ['menuPrefix' => 'Mobile', 'dismissOffcanvas' => true])
                </div>
            </div>
            <aside class="col-md-2 sidebar p-3 d-none d-md-block">
                @include('partials.sidebar')
            </aside>
            <main class="col-12 col-md-10 p-4">
            @else
            <main class="col-12 p-4">
            @endauth
                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif
                @if($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                @yield('content')
            </main>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://code.jquery.com/ui/1.14.1/jquery-ui.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        $(function() { $('.datepicker').datepicker({ dateFormat: 'yy-mm-dd' }); });
    </script>
    @stack('scripts')
</body>
</html>
