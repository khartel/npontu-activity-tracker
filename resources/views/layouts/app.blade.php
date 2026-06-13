<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') — Npontu Activity Tracker</title>

    {{-- Google Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Space+Grotesk:wght@500;600;700&display=swap" rel="stylesheet">

    {{-- Lucide Icons (CDN) --}}
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.min.js"></script>

    <style>
        /* ── Design Tokens ──────────────────────────────────────────────── */
        :root {
            --color-brand:        #1a6b3a;   /* Npontu deep green */
            --color-brand-light:  #22883f;
            --color-brand-pale:   #e8f5ec;
            --color-accent:       #f4a800;   /* Gold accent */
            --color-surface:      #f7f8fa;
            --color-card:         #ffffff;
            --color-border:       #e4e7ec;
            --color-text:         #1a1f2e;
            --color-text-muted:   #6b7280;
            --color-text-light:   #9ca3af;
            --color-done:         #16a34a;
            --color-pending:      #d97706;
            --color-inprogress:   #2563eb;
            --color-skipped:      #6b7280;
            --color-danger:       #dc2626;
            --radius:             8px;
            --radius-lg:          12px;
            --shadow-sm:          0 1px 3px rgba(0,0,0,.08);
            --shadow-md:          0 4px 12px rgba(0,0,0,.10);
            --font-display:       'Space Grotesk', sans-serif;
            --font-body:          'Inter', sans-serif;
        }

        /* ── Reset & Base ───────────────────────────────────────────────── */
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        html, body { height: 100%; }
        body {
            font-family: var(--font-body);
            background: var(--color-surface);
            color: var(--color-text);
            font-size: 14px;
            line-height: 1.6;
        }

        /* ── Layout Shell ───────────────────────────────────────────────── */
        .app-shell {
            display: flex;
            height: 100vh;
            overflow: hidden;
        }

        /* ── Sidebar ────────────────────────────────────────────────────── */
        .sidebar {
            width: 240px;
            flex-shrink: 0;
            background: var(--color-brand);
            display: flex;
            flex-direction: column;
            overflow-y: auto;
        }
        .sidebar-logo {
            padding: 24px 20px 20px;
            border-bottom: 1px solid rgba(255,255,255,.12);
        }
        .sidebar-logo-mark {
            font-family: var(--font-display);
            font-size: 18px;
            font-weight: 700;
            color: #fff;
            letter-spacing: -.3px;
        }
        .sidebar-logo-mark span { color: var(--color-accent); }
        .sidebar-logo-sub {
            font-size: 11px;
            color: rgba(255,255,255,.55);
            margin-top: 2px;
            text-transform: uppercase;
            letter-spacing: .8px;
        }

        .sidebar-nav {
            padding: 16px 12px;
            flex: 1;
        }
        .nav-section-label {
            font-size: 10px;
            font-weight: 600;
            color: rgba(255,255,255,.4);
            text-transform: uppercase;
            letter-spacing: 1px;
            padding: 0 8px;
            margin: 16px 0 6px;
        }
        .nav-item {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 9px 12px;
            border-radius: var(--radius);
            color: rgba(255,255,255,.75);
            text-decoration: none;
            font-size: 13.5px;
            font-weight: 500;
            transition: background .15s, color .15s;
            margin-bottom: 2px;
        }
        .nav-item:hover { background: rgba(255,255,255,.12); color: #fff; }
        .nav-item.active {
            background: rgba(255,255,255,.18);
            color: #fff;
        }
        .nav-item i { opacity: .85; }

        .sidebar-footer {
            padding: 16px 12px;
            border-top: 1px solid rgba(255,255,255,.12);
        }
        .user-card {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 8px;
            border-radius: var(--radius);
        }
        .user-avatar {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: var(--color-accent);
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: var(--font-display);
            font-weight: 700;
            font-size: 13px;
            color: var(--color-brand);
            flex-shrink: 0;
        }
        .user-name {
            font-size: 13px;
            font-weight: 600;
            color: #fff;
            line-height: 1.2;
        }
        .user-role {
            font-size: 11px;
            color: rgba(255,255,255,.5);
            text-transform: capitalize;
        }
        .logout-btn {
            display: flex;
            align-items: center;
            gap: 8px;
            width: 100%;
            margin-top: 8px;
            padding: 8px 12px;
            border: none;
            background: rgba(255,255,255,.08);
            color: rgba(255,255,255,.7);
            border-radius: var(--radius);
            font-size: 13px;
            cursor: pointer;
            transition: background .15s;
        }
        .logout-btn:hover { background: rgba(255,255,255,.16); color: #fff; }

        /* ── Main Content ───────────────────────────────────────────────── */
        .main-content {
            flex: 1;
            overflow-y: auto;
            display: flex;
            flex-direction: column;
        }
        .topbar {
            background: var(--color-card);
            border-bottom: 1px solid var(--color-border);
            padding: 0 28px;
            height: 60px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-shrink: 0;
        }
        .page-title {
            font-family: var(--font-display);
            font-size: 16px;
            font-weight: 600;
            color: var(--color-text);
        }
        .topbar-right {
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .page-body {
            flex: 1;
            padding: 28px;
        }

        /* ── Cards ──────────────────────────────────────────────────────── */
        .card {
            background: var(--color-card);
            border: 1px solid var(--color-border);
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-sm);
        }
        .card-header {
            padding: 18px 20px 14px;
            border-bottom: 1px solid var(--color-border);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .card-title {
            font-family: var(--font-display);
            font-size: 15px;
            font-weight: 600;
        }
        .card-body { padding: 20px; }

        /* ── Status Badges ──────────────────────────────────────────────── */
        .badge {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 3px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            line-height: 1.4;
        }
        .badge-done        { background: #dcfce7; color: var(--color-done); }
        .badge-pending     { background: #fef3c7; color: var(--color-pending); }
        .badge-in_progress { background: #dbeafe; color: var(--color-inprogress); }
        .badge-skipped     { background: #f3f4f6; color: var(--color-skipped); }

        /* ── Buttons ────────────────────────────────────────────────────── */
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 8px 16px;
            border-radius: var(--radius);
            font-size: 13.5px;
            font-weight: 600;
            cursor: pointer;
            border: none;
            transition: opacity .15s, transform .1s;
            text-decoration: none;
            line-height: 1;
        }
        .btn:active { transform: scale(.98); }
        .btn-primary { background: var(--color-brand); color: #fff; }
        .btn-primary:hover { background: var(--color-brand-light); }
        .btn-secondary { background: var(--color-surface); border: 1px solid var(--color-border); color: var(--color-text); }
        .btn-secondary:hover { background: #eef0f4; }
        .btn-danger { background: var(--color-danger); color: #fff; }
        .btn-sm { padding: 5px 12px; font-size: 12.5px; }
        .btn-ghost { background: transparent; color: var(--color-text-muted); }
        .btn-ghost:hover { background: var(--color-surface); }

        /* ── Forms ──────────────────────────────────────────────────────── */
        .form-group { margin-bottom: 16px; }
        .form-label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            color: var(--color-text);
            margin-bottom: 6px;
        }
        .form-control {
            width: 100%;
            padding: 9px 12px;
            border: 1px solid var(--color-border);
            border-radius: var(--radius);
            font-size: 13.5px;
            font-family: var(--font-body);
            color: var(--color-text);
            background: var(--color-card);
            transition: border-color .15s, box-shadow .15s;
            outline: none;
        }
        .form-control:focus {
            border-color: var(--color-brand);
            box-shadow: 0 0 0 3px rgba(26,107,58,.15);
        }
        select.form-control { appearance: none; background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%236b7280' stroke-width='2.5'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E"); background-repeat: no-repeat; background-position: right 10px center; padding-right: 32px; }
        .form-error { font-size: 12px; color: var(--color-danger); margin-top: 4px; }
        textarea.form-control { resize: vertical; min-height: 80px; }

        /* ── Alerts / Flash ─────────────────────────────────────────────── */
        .alert {
            padding: 12px 16px;
            border-radius: var(--radius);
            font-size: 13.5px;
            margin-bottom: 20px;
            display: flex;
            align-items: flex-start;
            gap: 10px;
        }
        .alert-success { background: #dcfce7; color: #15803d; border: 1px solid #bbf7d0; }
        .alert-error   { background: #fee2e2; color: #dc2626; border: 1px solid #fecaca; }
        .alert-warning { background: #fef3c7; color: #b45309; border: 1px solid #fde68a; }

        /* ── Tables ─────────────────────────────────────────────────────── */
        .data-table { width: 100%; border-collapse: collapse; font-size: 13.5px; }
        .data-table th {
            text-align: left;
            padding: 11px 16px;
            font-size: 11.5px;
            font-weight: 600;
            color: var(--color-text-muted);
            text-transform: uppercase;
            letter-spacing: .5px;
            background: var(--color-surface);
            border-bottom: 1px solid var(--color-border);
        }
        .data-table td {
            padding: 13px 16px;
            border-bottom: 1px solid var(--color-border);
            vertical-align: middle;
        }
        .data-table tr:last-child td { border-bottom: none; }
        .data-table tbody tr:hover { background: #fafbfc; }

        /* ── Utilities ──────────────────────────────────────────────────── */
        .text-muted  { color: var(--color-text-muted); }
        .text-sm     { font-size: 12.5px; }
        .text-xs     { font-size: 11.5px; }
        .font-medium { font-weight: 500; }
        .font-bold   { font-weight: 700; }
        .flex        { display: flex; }
        .items-center{ align-items: center; }
        .gap-8       { gap: 8px; }
        .gap-12      { gap: 12px; }
        .mt-4        { margin-top: 4px; }
        .mt-8        { margin-top: 8px; }
        .mb-16       { margin-bottom: 16px; }
        .mb-24       { margin-bottom: 24px; }

        /* ── Modal ──────────────────────────────────────────────────────── */
        .modal-overlay {
            position: fixed; inset: 0;
            background: rgba(0,0,0,.45);
            display: flex; align-items: center; justify-content: center;
            z-index: 1000;
            opacity: 0; pointer-events: none;
            transition: opacity .2s;
        }
        .modal-overlay.open { opacity: 1; pointer-events: all; }
        .modal {
            background: var(--color-card);
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-md);
            width: 100%;
            max-width: 520px;
            max-height: 90vh;
            overflow-y: auto;
            transform: translateY(12px);
            transition: transform .2s;
        }
        .modal-overlay.open .modal { transform: translateY(0); }
        .modal-header {
            padding: 20px 24px 16px;
            border-bottom: 1px solid var(--color-border);
            display: flex; align-items: center; justify-content: space-between;
        }
        .modal-title {
            font-family: var(--font-display);
            font-size: 16px; font-weight: 600;
        }
        .modal-close {
            background: none; border: none; cursor: pointer;
            color: var(--color-text-muted); padding: 4px;
        }
        .modal-body { padding: 20px 24px 24px; }

        /* ── Responsive ─────────────────────────────────────────────────── */
        @media (max-width: 768px) {
            .sidebar { width: 200px; }
            .page-body { padding: 16px; }
        }
    </style>

    @stack('styles')
</head>
<body>
<div class="app-shell">

    {{-- ── Sidebar ── --}}
    <aside class="sidebar">
        <div class="sidebar-logo">
            <div class="sidebar-logo-mark">NP<span>O</span>NTU</div>
            <div class="sidebar-logo-sub">Activity Tracker</div>
        </div>

        <nav class="sidebar-nav">
            <div class="nav-section-label">Main</div>

            <a href="{{ route('dashboard') }}"
               class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <i data-lucide="layout-dashboard" width="16" height="16"></i>
                Daily Dashboard
            </a>

            <a href="{{ route('reports.index') }}"
               class="nav-item {{ request()->routeIs('reports.*') ? 'active' : '' }}">
                <i data-lucide="bar-chart-2" width="16" height="16"></i>
                Reports
            </a>

            @if(auth()->user()->isAdmin())
                <div class="nav-section-label">Admin</div>

                <a href="{{ route('activities.index') }}"
                   class="nav-item {{ request()->routeIs('activities.*') ? 'active' : '' }}">
                    <i data-lucide="list-checks" width="16" height="16"></i>
                    Manage Activities
                </a>

                <a href="{{ route('users.index') }}"
                   class="nav-item {{ request()->routeIs('users.*') ? 'active' : '' }}">
                    <i data-lucide="users" width="16" height="16"></i>
                    Team Members
                </a>
            @endif
        </nav>

        <div class="sidebar-footer">
            <div class="user-card">
                <div class="user-avatar">
                    {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                </div>
                <div>
                    <div class="user-name">{{ auth()->user()->name }}</div>
                    <div class="user-role">{{ auth()->user()->role }}</div>
                </div>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="logout-btn">
                    <i data-lucide="log-out" width="14" height="14"></i>
                    Sign Out
                </button>
            </form>
        </div>
    </aside>

    {{-- ── Main ── --}}
    <div class="main-content">
        <div class="topbar">
            <div class="page-title">@yield('page-title', 'Dashboard')</div>
            <div class="topbar-right">
                <span class="text-sm text-muted">
                    <i data-lucide="calendar" width="13" height="13" style="display:inline;vertical-align:middle;margin-right:4px"></i>
                    {{ now()->format('D, d M Y') }}
                </span>
                @yield('topbar-actions')
            </div>
        </div>

        <div class="page-body">
            @if(session('success'))
                <div class="alert alert-success">
                    <i data-lucide="check-circle" width="16" height="16" style="flex-shrink:0;margin-top:1px"></i>
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error') || $errors->has('error'))
                <div class="alert alert-error">
                    <i data-lucide="alert-circle" width="16" height="16" style="flex-shrink:0;margin-top:1px"></i>
                    {{ session('error') ?? $errors->first('error') }}
                </div>
            @endif

            @yield('content')
        </div>
    </div>
</div>

<script>
    // Initialise Lucide icons
    document.addEventListener('DOMContentLoaded', () => lucide.createIcons());

    // CSRF helper for AJAX
    window.csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
</script>

@stack('scripts')
</body>
</html>
