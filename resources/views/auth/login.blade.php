<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - SAP.HRIS</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        html { font-family: 'Manrope', sans-serif; }
        .login-shell { box-shadow: 0 32px 90px rgba(0, 8, 24, .62), 0 0 0 1px rgba(145, 201, 255, .08); }
        .brand-grid { background-image: linear-gradient(135deg, rgba(145, 201, 255, .06) 1px, transparent 1px); background-size: 32px 32px; }
        .moon-orbit { box-shadow: 0 0 44px rgba(133, 196, 255, .14); }
        .field { background: rgba(7, 22, 48, .56); border-color: rgba(146, 191, 233, .18); }
        .field:focus-within { border-color: #91c9ff; box-shadow: 0 0 0 4px rgba(109, 177, 243, .12), 0 0 24px rgba(109, 177, 243, .08); }
        .field input::placeholder { color: #7185a5; }
        @keyframes float-logo {
            0%, 100% { transform: translateY(0) rotate(-2deg); }
            50% { transform: translateY(-10px) rotate(2deg); }
        }
        .float-logo { animation: float-logo 5s ease-in-out infinite; }
        @media (max-width: 767px) { .brand-panel { min-height: 370px; } }
    </style>
</head>
<body class="min-h-screen bg-[#020817] flex items-center justify-center p-3 sm:p-6">

    <div class="login-shell w-full max-w-6xl bg-[#07152e] rounded-[28px] overflow-hidden grid md:grid-cols-[1.12fr_.88fr] border border-[#91c9ff]/15">

        <div class="brand-panel relative bg-gradient-to-br from-[#0d2d5a] via-[#0a2148] to-[#050f26] p-8 md:p-12 flex flex-col justify-between text-white overflow-hidden">
            <div class="brand-grid absolute inset-0 opacity-70"></div>

            <div class="relative z-10">
                <div class="flex items-center justify-between gap-3 mb-20">
                    <div class="flex items-center gap-3">
                    <div class="w-11 h-11 bg-[#9bd0ff] rounded-xl flex items-center justify-center text-[#09204a] shadow-[0_0_22px_rgba(155,208,255,.2)]">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 3l7 4v5c0 4.5-3 7.5-7 9-4-1.5-7-4.5-7-9V7l7-4z" /><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4" />
                        </svg>
                    </div>
                    <div>
                        <p class="font-extrabold leading-none tracking-tight">SAP.HRIS</p>
                        <p class="text-xs text-blue-100/60 mt-1">Sawita Group</p>
                    </div>
                    </div>
                </div>

                <div class="mb-8 max-w-md">
                    <p class="text-xs font-bold uppercase tracking-[0.28em] text-[#a9d5ff]/70 mb-6">Human resources information system</p>
                    <div class="float-logo mb-8 flex h-28 w-28 items-center justify-center drop-shadow-[0_0_22px_rgba(164,211,255,.2)]">
                        <img src="{{ asset('images/pertamina-logo.svg') }}" alt="Pertamina" class="h-24 w-24 object-contain">
                    </div>
                    <h1 class="text-4xl md:text-5xl font-extrabold leading-[1.06] tracking-tight mb-5">Semua tim,<br><span class="text-[#b9e0ff]">satu kendali.</span></h1>
                    <p class="text-blue-50/70 text-sm leading-relaxed max-w-sm">Satu sistem untuk menjaga ritme kerja tim kebun dan pabrik tetap terukur.</p>
                </div>

                <div class="flex items-center gap-5 max-w-sm border-t border-blue-100/10 pt-5">
                    <div><p class="text-xl font-extrabold text-white">24/7</p><p class="text-[11px] text-blue-100/60 mt-1">Akses terpusat</p></div>
                    <div class="h-8 w-px bg-blue-100/15"></div>
                    <div><p class="text-xl font-extrabold text-white">Real-time</p><p class="text-[11px] text-blue-100/60 mt-1">Data operasional</p></div>
                </div>
            </div>

            <div class="relative z-10 mt-12 text-xs text-blue-100/45">
                <span>&copy; {{ date('Y') }} SAP.HRIS</span>
            </div>
        </div>

        <div class="p-8 md:p-16 flex flex-col justify-center bg-gradient-to-br from-[#0b1e3d] via-[#07152f] to-[#061126]">
            <div class="mb-10">
            <p class="text-xs font-bold uppercase tracking-[0.2em] text-[#9bcfff] mb-3">Welcome back</p>
                <h2 class="text-3xl font-extrabold tracking-tight text-white mb-2">Selamat datang kembali</h2>
            <p class="text-sm text-blue-100/55">Gunakan akun perusahaan untuk melanjutkan ke dashboard.</p>
            </div>

            @if ($errors->any())
                <div class="bg-rose-950/50 border border-rose-400/20 text-rose-200 text-sm rounded-xl p-3 mb-5 flex items-start gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 mt-0.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                    </svg>
                    <span>{{ $errors->first() }}</span>
                </div>
            @endif

            <form method="POST" action="{{ route('login.submit') }}" class="space-y-5">
                @csrf

                <div>
                    <label class="block text-xs font-bold text-blue-100/80 mb-2">Username</label>
                    <div class="field relative flex items-center rounded-xl border border-slate-200 bg-white transition">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-blue-200/45 absolute left-3.5 top-1/2 -translate-y-1/2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                        </svg>
                        <input type="text" name="username" value="{{ old('username') }}" required autofocus class="w-full bg-transparent text-blue-50 pl-10 pr-3 py-3 text-sm focus:outline-none" placeholder="Masukkan username">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-blue-100/80 mb-2">Password</label>
                    <div class="field relative flex items-center rounded-xl border border-slate-200 bg-white transition">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-blue-200/45 absolute left-3.5 top-1/2 -translate-y-1/2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" />
                        </svg>
                           <input type="password" name="password" required class="w-full bg-transparent text-blue-50 pl-10 pr-3 py-3 text-sm focus:outline-none" placeholder="Masukkan password">
                    </div>
                </div>

                <button type="submit"
                        class="w-full bg-[#8fc9ff] hover:bg-[#b3ddff] text-[#07152e] font-bold py-3.5 rounded-xl shadow-lg shadow-blue-950/30 transition-all flex items-center justify-center gap-2">
                    Lanjutkan ke dashboard
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" />
                    </svg>
                </button>
            </form>

            <div class="mt-5 flex justify-between text-xs font-semibold">
                <a href="{{ route('password.request') }}" class="text-[#a4d3ff] hover:text-white">Lupa password?</a>
                <a href="{{ route('register') }}" class="text-[#a4d3ff] hover:text-white">Buat akun baru</a>
            </div>

            <div class="mt-10 border-t border-blue-100/10 pt-5">
                <div class="flex items-start gap-3">
                    <div class="mt-0.5 flex h-7 w-7 shrink-0 items-center justify-center rounded-lg bg-emerald-400/10 text-emerald-300">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    </div>
                    <div><p class="text-xs font-bold text-blue-50/80">Akses dilindungi</p><p class="text-xs text-blue-100/45 mt-1 leading-relaxed">Sesi login terenkripsi dan tercatat untuk keamanan operasional.</p></div>
                </div>
            </div>
        </div>
    </div>

</body>
</html>