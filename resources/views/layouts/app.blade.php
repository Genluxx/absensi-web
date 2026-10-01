<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'SAP.HRIS')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --theme-accent: oklch(90.1% 0.076 70.697);
            --bg-1: #071523;
            --bg-2: #0c2033;
            --panel: #102a43;
            --panel-strong: #0b1f33;
            --line: rgba(148, 163, 184, 0.14);
            --blue: oklch(90.1% 0.076 70.697);
            --blue-soft: color-mix(in srgb, var(--theme-accent) 16%, transparent);
            --cyan: oklch(90.1% 0.076 70.697);
        }

        html { font-family: 'Manrope', sans-serif; }

        body {
            background: linear-gradient(135deg, #071523 0%, #0c2033 58%, #102a43 100%);
        }

        .enterprise-sidebar { background: rgba(5, 18, 32, 0.88); border-color: rgba(148, 163, 184, 0.12); }
        .enterprise-topbar { background: rgba(7, 21, 35, 0.78); border-color: rgba(148, 163, 184, 0.14); }
        .enterprise-nav-link { border-radius: 10px; color: #9fb2c7; }
        .enterprise-nav-link:hover { background: rgba(79, 140, 255, 0.1) !important; border-color: rgba(105, 210, 231, 0.18) !important; color: #eef6ff !important; transform: translateX(2px); box-shadow: none !important; }
        .enterprise-nav-link.bg-blue-500\/15 { background: rgba(79, 140, 255, 0.16) !important; border-color: rgba(105, 210, 231, 0.26) !important; color: #d9f5ff !important; box-shadow: inset 3px 0 0 #69d2e7 !important; }
        .enterprise-panel, .glass-panel { background: rgba(16, 42, 67, 0.84); border-color: var(--line); border-radius: 16px; box-shadow: 0 14px 36px rgba(1, 12, 24, 0.2); }
        .enterprise-mobile-nav { background: rgba(5, 18, 32, 0.92); border-color: rgba(148, 163, 184, 0.14); }
        .theme-accent-bg { background-color: var(--theme-accent) !important; }
        .text-cyan-300, .text-cyan-200, .text-blue-300, .text-blue-200 { color: var(--theme-accent) !important; }
        .bg-blue-500\/15, .bg-blue-500\/20 { background-color: color-mix(in srgb, var(--theme-accent) 16%, transparent) !important; }
        .border-blue-400\/30, .border-cyan-300, .border-cyan-400\/50 { border-color: color-mix(in srgb, var(--theme-accent) 55%, transparent) !important; }
        .bg-blue-500, .bg-blue-600 { background-color: var(--theme-accent) !important; color: #172033 !important; }

        .nav-link {
            transition: all 0.2s ease;
            border: 1px solid transparent;
        }

        .nav-link:hover {
            transform: translateX(2px);
        }

        .glow-button {
            box-shadow: 0 8px 20px rgba(2, 6, 23, 0.22);
            transition: all 0.2s ease;
        }

        .glow-button:hover {
            transform: translateY(-1px);
            box-shadow: 0 10px 24px rgba(2, 6, 23, 0.3);
        }

        /* Shared cream-white application theme. */
        .app-shell {
            --app-cream: #f8f5f0;
            --app-ivory: #fffdf9;
            --app-ink: #2c241d;
            --app-muted: #65594e;
            --app-blue: #60a5fa;
            --app-gold: #d7a94b;
            --app-gold-light: #edc66f;
            --app-gold-dark: #a97924;
            --app-line: rgba(112, 92, 68, 0.16);
            background:
                radial-gradient(circle at top right, rgba(96, 165, 250, 0.12), transparent 28%),
                linear-gradient(135deg, #f8f5f0 0%, #fffdf9 54%, #f4eadb 100%);
            color: var(--app-ink);
            font-size: 15px;
            line-height: 1.55;
        }

        .app-shell h1,
        .app-shell h2,
        .app-shell h3,
        .app-shell p,
        .app-shell label,
        .app-shell th,
        .app-shell td,
        .app-shell button,
        .app-shell input,
        .app-shell select,
        .app-shell textarea {
            text-rendering: optimizeLegibility;
        }

        .app-shell [class*="text-[10px]"] {
            font-size: 0.7rem !important;
        }

        .app-shell [class*="text-[11px]"] {
            font-size: 0.76rem !important;
        }

        .app-shell [class*="text-xs"] {
            font-size: 0.8rem !important;
        }

        .app-shell [class*="text-slate-400"],
        .app-shell [class*="text-slate-500"] {
            color: var(--app-muted) !important;
            font-weight: 500;
        }

        .app-shell [class*="text-slate-300"],
        .app-shell [class*="text-slate-200"] {
            color: #4e4339 !important;
        }

        .app-shell [class*="text-cyan-100"],
        .app-shell [class*="text-cyan-200"],
        .app-shell [class*="text-cyan-300"],
        .app-shell [class*="text-blue-100"],
        .app-shell [class*="text-blue-200"],
        .app-shell [class*="text-blue-300"] {
            color: #1d4ed8 !important;
        }

        .app-shell [class*="text-emerald-200"],
        .app-shell [class*="text-emerald-300"],
        .app-shell [class*="text-emerald-400"] {
            color: #16734b !important;
        }

        .app-shell [class*="text-amber-200"],
        .app-shell [class*="text-amber-300"],
        .app-shell [class*="text-orange-300"] {
            color: #8a5b12 !important;
        }

        .app-shell [class*="text-rose-200"],
        .app-shell [class*="text-rose-300"],
        .app-shell [class*="text-red-200"],
        .app-shell [class*="text-red-300"] {
            color: #b42318 !important;
        }

        .app-shell [class*="text-yellow-200"],
        .app-shell [class*="text-yellow-300"] {
            color: #806000 !important;
        }

        .app-shell .enterprise-sidebar,
        .app-shell .enterprise-mobile-nav {
            background: rgba(255, 253, 249, 0.86);
            border-color: var(--app-line);
            color: var(--app-ink);
            box-shadow: 8px 0 28px rgba(93, 72, 45, 0.06);
        }

        .app-shell .enterprise-topbar {
            background: rgba(255, 253, 249, 0.78);
            border-color: var(--app-line);
            color: var(--app-ink);
        }

        .app-shell > .flex-1 {
            background: transparent !important;
        }

        .app-shell .enterprise-panel,
        .app-shell .glass-panel {
            background: rgba(255, 253, 249, 0.78);
            border-color: var(--app-line);
            box-shadow: 0 14px 36px rgba(93, 72, 45, 0.08);
            backdrop-filter: blur(10px);
        }

        .app-shell [class*="bg-slate-950"],
        .app-shell [class*="bg-slate-900"],
        .app-shell [class*="bg-slate-800"],
        .app-shell [class*="bg-slate-700"] {
            background-color: rgba(255, 255, 255, 0.72) !important;
        }

        .app-shell [class*="border-slate-800"],
        .app-shell [class*="border-slate-700"],
        .app-shell [class*="border-slate-600"] {
            border-color: var(--app-line) !important;
        }

        .app-shell [class*="text-white"],
        .app-shell [class*="text-slate-100"],
        .app-shell [class*="text-slate-200"],
        .app-shell [class*="text-slate-300"] {
            color: var(--app-ink) !important;
        }

        .app-shell [class*="text-slate-400"],
        .app-shell [class*="text-slate-500"] {
            color: var(--app-muted) !important;
        }

        .app-shell .enterprise-nav-link {
            color: #75695d !important;
        }

        .app-shell .enterprise-nav-link:hover,
        .app-shell .enterprise-nav-link.bg-blue-500\/15 {
            background: rgba(96, 165, 250, 0.12) !important;
            border-color: rgba(59, 130, 246, 0.28) !important;
            color: #1d4ed8 !important;
            box-shadow: inset 3px 0 0 #60a5fa !important;
        }

        .app-shell input,
        .app-shell select,
        .app-shell textarea {
            background-color: rgba(255, 255, 255, 0.86) !important;
            border-color: rgba(96, 165, 250, 0.28) !important;
            color: var(--app-ink) !important;
        }

        .app-shell input::placeholder,
        .app-shell textarea::placeholder {
            color: #9a8d7f !important;
        }

        .app-shell input:focus,
        .app-shell select:focus,
        .app-shell textarea:focus {
            border-color: #3b82f6 !important;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.12) !important;
            outline: none;
        }

        .app-shell .bg-blue-600,
        .app-shell .bg-blue-500 {
            background: linear-gradient(135deg, var(--app-gold-light), var(--app-gold)) !important;
            color: #3b2a13 !important;
        }

        .app-shell button[type="submit"],
        .app-shell button.bg-cyan-400,
        .app-shell button.bg-blue-600,
        .app-shell button.bg-blue-500,
        .app-shell .glow-button {
            background: linear-gradient(135deg, var(--app-gold-light), var(--app-gold)) !important;
            border-color: rgba(167, 121, 36, 0.4) !important;
            color: #3b2a13 !important;
            box-shadow: 0 10px 22px rgba(167, 121, 36, 0.18);
        }

        .app-shell button[type="submit"]:hover,
        .app-shell button.bg-cyan-400:hover,
        .app-shell button.bg-blue-600:hover,
        .app-shell button.bg-blue-500:hover,
        .app-shell .glow-button:hover {
            background: linear-gradient(135deg, #f2d487, var(--app-gold-dark)) !important;
            color: #2c241d !important;
            box-shadow: 0 12px 26px rgba(167, 121, 36, 0.26);
        }

        .app-shell .enterprise-topbar a,
        .app-shell .enterprise-topbar button {
            color: #2563eb;
        }

        .app-shell a.export-action,
        .app-shell a.export-action:hover {
            color: #ffffff !important;
        }

        .app-shell #globalRealtimeClock {
            color: var(--app-gold-dark) !important;
            font-size: 1.15rem;
            font-weight: 800;
            letter-spacing: 0.04em;
        }

        .app-shell .realtime-display {
            color: var(--app-gold-dark) !important;
            line-height: 1.35;
        }

        .app-shell button.delete-action,
        .app-shell button.delete-action:hover {
            color: #b42318 !important;
            background: #fff1f0 !important;
            border: 1px solid rgba(180, 35, 24, 0.28) !important;
            box-shadow: none !important;
        }

        .app-shell button.delete-action:hover {
            background: #ffe1de !important;
        }

        .app-shell button.logout-action {
            display: inline-flex;
            align-items: center;
            gap: 0.45rem;
            border: 1px solid rgba(37, 99, 235, 0.28) !important;
            border-radius: 0.65rem;
            background: rgba(96, 165, 250, 0.1) !important;
            padding: 0.5rem 0.75rem;
            color: #1d4ed8 !important;
            font-size: 0.8rem;
            font-weight: 800;
            transition: all 0.2s ease;
        }

        .app-shell button.logout-action:hover {
            background: rgba(96, 165, 250, 0.2) !important;
            border-color: rgba(37, 99, 235, 0.45) !important;
            transform: translateY(-1px);
        }

        .app-shell section[class*="bg-[#123653"] {
            background: linear-gradient(135deg, #f7eedf 0%, #f4e7cc 58%, #fffdf9 100%) !important;
            border-color: rgba(96, 165, 250, 0.22) !important;
            box-shadow: 0 18px 42px rgba(93, 72, 45, 0.1) !important;
        }

        .app-shell section[class*="bg-[#123653"] [class*="text-white"] {
            color: var(--app-ink) !important;
        }

    </style>
</head>
<body class="app-shell min-h-screen flex overflow-x-hidden">

    <!-- Sidebar -->
    <aside class="enterprise-sidebar hidden lg:flex w-72 border-r min-h-screen flex-col text-slate-200">
        <div class="p-6 border-b border-slate-800/70 flex items-center gap-3">
            <div class="w-10 h-10 theme-accent-bg rounded-xl flex items-center justify-center text-slate-900 shadow-lg shadow-blue-500/20">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 11c0 3.5-2.5 6.5-6 8-3.5-1.5-6-4.5-6-8V5l6-3 6 3v6z" transform="translate(6,0)" />
                </svg>
            </div>
            <div>
                <p class="font-bold text-sm leading-none text-white">SAP.HRIS</p>
                <p class="text-xs text-slate-400">Sawita</p>
            </div>
        </div>

        <nav class="flex-1 p-5 space-y-1">
            <p class="text-[10px] font-bold tracking-[0.2em] text-slate-500 px-3 mb-2 mt-2">WORKSPACE</p>
            <a href="{{ route('dashboard') }}"
               class="enterprise-nav-link nav-link flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-semibold
                      {{ request()->routeIs('dashboard') ? 'bg-blue-500/15 text-blue-300 border border-blue-400/30 shadow-[0_0_18px_rgba(59,130,246,0.2)]' : 'text-slate-300 hover:bg-slate-800/80 hover:text-white' }}">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6a2.25 2.25 0 012.25-2.25h12A2.25 2.25 0 0120.25 6v2.25a2.25 2.25 0 01-2.25 2.25h-12a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75a2.25 2.25 0 012.25-2.25h5.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25v-2.25zM15.75 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-.75a2.25 2.25 0 01-2.25-2.25v-2.25z" />
                </svg>
                Dashboard
            </a>

            <p class="text-[10px] font-bold tracking-[0.2em] text-slate-500 px-3 mb-2 mt-7">ATTENDANCE</p>

            @if (auth()->user()->hasPermission('presensi.input') && auth()->user()->role !== 'karyawan')
            <a href="{{ route('presensi.input') }}"
               class="enterprise-nav-link nav-link flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-semibold
                      {{ request()->routeIs('presensi.input') ? 'bg-blue-500/15 text-blue-300 border border-blue-400/30 shadow-[0_0_18px_rgba(59,130,246,0.2)]' : 'text-slate-300 hover:bg-slate-800/80 hover:text-white' }}">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                Input Presensi Tim
            </a>
            @endif

            @if (auth()->user()->hasPermission('presensi.view'))
            <a href="{{ route('presensi.log') }}"
               class="enterprise-nav-link nav-link flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-semibold
                      {{ request()->routeIs('presensi.log') ? 'bg-blue-500/15 text-blue-300 border border-blue-400/30 shadow-[0_0_18px_rgba(59,130,246,0.2)]' : 'text-slate-300 hover:bg-slate-800/80 hover:text-white' }}">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m5.231 13.481L15 17.25m-1.519-2.639L14.25 12.75m-2.62 3.109L9.75 17.25m2.5-2.639L9.75 12.75M3.75 21h16.5M4.5 3.75h6.879a1.5 1.5 0 011.06.44l4.622 4.62a1.5 1.5 0 01.44 1.061V19.5a1.5 1.5 0 01-1.5 1.5h-9.75a1.5 1.5 0 01-1.5-1.5V5.25a1.5 1.5 0 011.5-1.5z" />
                </svg>
                Log & Laporan
            </a>
            @endif

            @if (auth()->user()->hasPermission('koreksi.request'))
            <a href="{{ route('koreksi.index') }}"
               class="enterprise-nav-link nav-link flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-semibold
                      {{ request()->routeIs('koreksi.*') ? 'bg-blue-500/15 text-blue-300 border border-blue-400/30 shadow-[0_0_18px_rgba(59,130,246,0.2)]' : 'text-slate-300 hover:bg-slate-800/80 hover:text-white' }}">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99" />
                </svg>
                Permintaan Koreksi
            </a>
            @endif

            @if (auth()->user()->hasPermission('karyawan.view'))
            <a href="{{ route('karyawan.index') }}"
               class="enterprise-nav-link nav-link flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-semibold
                      {{ request()->routeIs('karyawan.*') ? 'bg-blue-500/15 text-blue-300 border border-blue-400/30 shadow-[0_0_18px_rgba(59,130,246,0.2)]' : 'text-slate-300 hover:bg-slate-800/80 hover:text-white' }}">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" />
                </svg>
                Kelola Karyawan
            </a>
            @endif

            @if (in_array(auth()->user()->role, ['admin_hr', 'super_admin']))
<p class="text-[10px] font-bold tracking-[0.2em] text-slate-500 px-3 mb-2 mt-7">ADMINISTRATION</p>
<a href="{{ route('mandor.index') }}"
               class="enterprise-nav-link nav-link flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-semibold
                      {{ request()->routeIs('mandor.*') ? 'bg-blue-500/15 text-blue-300 border border-blue-400/30 shadow-[0_0_18px_rgba(59,130,246,0.2)]' : 'text-slate-300 hover:bg-slate-800/80 hover:text-white' }}">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17.982 18.725A7.488 7.488 0 0012 15.75a7.488 7.488 0 00-5.982 2.975m11.963 0a9 9 0 10-11.963 0m11.963 0A8.966 8.966 0 0112 21a8.966 8.966 0 01-5.982-2.275M15 9.75a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
                Kelola Mandor
            </a>
            @if (in_array(auth()->user()->role, ['admin_hr', 'super_admin']))
            <a href="{{ route('audit.index') }}"
               class="enterprise-nav-link nav-link flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-semibold
                      {{ request()->routeIs('audit.*') ? 'bg-blue-500/15 text-blue-300 border border-blue-400/30' : 'text-slate-300 hover:bg-slate-800/80 hover:text-white' }}">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75h6m-6 3h6m-6-6h6m3.75-6.75H7.5A2.25 2.25 0 005.25 5.25v13.5A2.25 2.25 0 007.5 21h9a2.25 2.25 0 002.25-2.25V5.25A2.25 2.25 0 0016.5 3z" />
                </svg>
                Audit Aktivitas
            </a>
            @if (auth()->user()->role === 'super_admin')
            <a href="{{ route('roles.index') }}"
               class="enterprise-nav-link nav-link flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-semibold
                      {{ request()->routeIs('roles.*') ? 'bg-blue-500/15 text-blue-300 border border-blue-400/30' : 'text-slate-300 hover:bg-slate-800/80 hover:text-white' }}">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75l3 3 6.75-6.75M21 12c0 4.97-4.03 9-9 9s-9-4.03-9-9 4.03-9 9-9 9 4.03 9 9z" />
                </svg>
                Role & Akses
            </a>
            @endif
            @endif
            @endif
        </nav>

        <div class="p-5 border-t border-slate-800/70 text-xs text-slate-500">
            <span class="text-slate-300 font-semibold">SAP.HRIS</span> · Sawita Group<br>Operational suite v1.0
        </div>
    </aside>

    <!-- Main content -->
    <div class="flex-1 min-w-0 flex flex-col bg-slate-900/30">
        <!-- Topbar -->
        <div class="enterprise-topbar border-b px-4 sm:px-8 py-4 flex justify-between items-center backdrop-blur-sm">
            <div class="text-sm text-slate-300">
                <span class="hidden sm:inline text-slate-500">SAP.HRIS</span><span class="hidden sm:inline mx-2 text-slate-600">/</span><span class="text-white font-semibold">@yield('page-title', 'Dashboard')</span>
            </div>
            <div class="flex items-center gap-4">
                <div class="hidden sm:block text-base sm:text-lg font-extrabold tracking-wide text-cyan-300" title="Waktu server lokal">
                    <span id="globalRealtimeClock">--:--:--</span>
                </div>
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0" />
                </svg>

                <a href="{{ route('profile.edit') }}" class="flex items-center gap-2 hover:opacity-80">
                    <div class="w-9 h-9 bg-gradient-to-br from-blue-500 to-cyan-600 rounded-full flex items-center justify-center text-white font-bold text-sm shadow-lg shadow-blue-500/30">
                        {{ substr(auth()->user()->name, 0, 1) }}
                    </div>
                    <div class="text-sm">
                        <p class="font-bold leading-none text-white">{{ auth()->user()->name }}</p>
                        <p class="text-xs text-slate-400">{{ ucwords(str_replace('_', ' ', auth()->user()->role)) }}</p>
                    </div>
                </a>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="logout-action" aria-label="Keluar dari akun">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6A2.25 2.25 0 005.25 5.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15m3-6l3 3m0 0l-3 3m3-3H9" />
                        </svg>
                        Logout
                    </button>
                </form>
            </div>
        </div>

        <div class="enterprise-mobile-nav lg:hidden border-b px-4 py-2 flex gap-2 overflow-x-auto">
            <a href="{{ route('dashboard') }}" class="shrink-0 rounded-lg px-3 py-2 text-xs font-bold {{ request()->routeIs('dashboard') ? 'bg-blue-500/20 text-cyan-200' : 'text-slate-400' }}">Dashboard</a>
            @if (auth()->user()->hasPermission('presensi.input') && auth()->user()->role !== 'karyawan')
                <a href="{{ route('presensi.input') }}" class="shrink-0 rounded-lg px-3 py-2 text-xs font-bold {{ request()->routeIs('presensi.input') ? 'bg-blue-500/20 text-cyan-200' : 'text-slate-400' }}">Input Presensi</a>
            @endif
            @if (auth()->user()->hasPermission('presensi.view'))
                <a href="{{ route('presensi.log') }}" class="shrink-0 rounded-lg px-3 py-2 text-xs font-bold {{ request()->routeIs('presensi.log') ? 'bg-blue-500/20 text-cyan-200' : 'text-slate-400' }}">Laporan</a>
            @endif
            @if (auth()->user()->hasPermission('koreksi.request'))
                <a href="{{ route('koreksi.index') }}" class="shrink-0 rounded-lg px-3 py-2 text-xs font-bold {{ request()->routeIs('koreksi.*') ? 'bg-blue-500/20 text-cyan-200' : 'text-slate-400' }}">Koreksi</a>
            @endif
        </div>

        <!-- Page content -->
        <div class="p-4 sm:p-6 flex-1 min-w-0">
            @if (session('success'))
                <div class="bg-green-900/40 border border-green-700/50 text-green-200 text-sm rounded-lg p-3 mb-4">{{ session('success') }}</div>
            @endif

            @if ($errors->any())
                <div class="bg-red-900/40 border border-red-700/50 text-red-200 text-sm rounded-lg p-3 mb-4">
                    <ul class="list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @yield('content')
        </div>
    </div>

</body>
<script>
    (() => {
        const clock = document.getElementById('globalRealtimeClock');
        if (!clock) return;
        const updateClock = () => {
            clock.textContent = new Intl.DateTimeFormat('id-ID', {
                hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: false,
            }).format(new Date());
        };
        updateClock();
        setInterval(updateClock, 1000);
    })();
</script>
</html>