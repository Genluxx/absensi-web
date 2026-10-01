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
        :root {
            --theme-accent: #e9d5a8;
            --theme-accent-strong: #d7b06f;
            --theme-blue: #60a5fa;
            --theme-blue-deep: #3b82f6;
            --panel-cream: #f5efe3;
            --panel-ivory: #fffdf9;
            --panel-deep: #f0e4cf;
            --text-primary: #2c241d;
            --text-soft: #61564b;
            --line-soft: rgba(112, 92, 68, 0.15);
            --shadow-soft: rgba(71, 54, 34, 0.12);
        }
        html, body { font-family: 'Manrope', 'Segoe UI', sans-serif; }
        body {
            background:
                radial-gradient(circle at top left, rgba(96, 165, 250, 0.18), transparent 24%),
                #f8f5f0;
        }
        .login-shell {
            box-shadow: 0 18px 48px rgba(93, 72, 45, 0.08), 0 0 0 1px rgba(112, 92, 68, 0.06);
            backdrop-filter: blur(8px);
        }
        .brand-grid { background-image: linear-gradient(135deg, rgba(140, 110, 74, .04) 1px, transparent 1px); background-size: 28px 28px; }
        .field {
            background: rgba(255, 255, 255, 0.92);
            border-color: rgba(96, 165, 250, 0.28);
            box-shadow: inset 0 1px 0 rgba(255,255,255,.8);
        }
        .field:focus-within {
            border-color: var(--theme-blue-deep);
            box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.12), 0 0 18px rgba(96, 165, 250, 0.10);
        }
        .field-icon {
            color: var(--theme-blue);
        }
        .field input { color: var(--text-primary); letter-spacing: .01em; }
        .field input::placeholder { color: #8d7f72; }
        .login-label { color: var(--text-primary); }
        .login-subtitle { color: var(--text-soft); }
        .login-help { color: var(--text-soft); }
        .login-link { color: #2563eb; }
        .login-link:hover { color: #1d4ed8; }
        .premium-button {
            background: linear-gradient(135deg, #60a5fa, #93c5fd);
            box-shadow: 0 18px 30px rgba(96, 165, 250, 0.22);
            color: #ffffff;
        }
        .premium-button:hover { box-shadow: 0 20px 36px rgba(96, 165, 250, 0.28); }
        .status-pill {
            background: rgba(255, 255, 255, 0.6);
            border: 1px solid rgba(129, 104, 76, 0.18);
            color: var(--text-primary);
        }
        .glass-card {
            background: rgba(255, 255, 255, 0.7);
            border: 1px solid rgba(130, 108, 82, 0.14);
            backdrop-filter: blur(10px);
        }
        .toggle-password {
            color: #60a5fa;
            transition: color 0.2s ease;
        }
        .toggle-password:hover {
            color: #2563eb;
        }
        @keyframes float-logo {
            0%, 100% { transform: translateY(0) rotate(-2deg); }
            50% { transform: translateY(-10px) rotate(2deg); }
        }
        .float-logo { animation: float-logo 5s ease-in-out infinite; }
        .theme-accent { color: var(--theme-accent-strong) !important; }
        .theme-accent-bg { background-color: var(--theme-accent) !important; }
        .theme-accent-border { border-color: var(--theme-accent-strong) !important; }
        @media (max-width: 767px) { .brand-panel { min-height: 370px; } }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center p-3 sm:p-6">

    <div class="login-shell w-full max-w-6xl bg-[#fffdf9] rounded-[28px] overflow-hidden grid md:grid-cols-[1.12fr_.88fr] border border-[#d7c29d]/30">

        <div class="brand-panel relative bg-gradient-to-br from-[#f7eedf] via-[#f4e7cc] to-[#faf8f4] p-8 md:p-10 flex flex-col justify-between text-[#2d241d] overflow-hidden">
            <div class="brand-grid absolute inset-0 opacity-70"></div>

            <div class="relative z-10">
                <div class="flex items-center gap-3 mb-12">
                    <div class="w-11 h-11 theme-accent-bg rounded-xl flex items-center justify-center text-[#402d1e] shadow-[0_0_18px_rgba(194,154,92,.15)]">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 3l7 4v5c0 4.5-3 7.5-7 9-4-1.5-7-4.5-7-9V7l7-4z" /><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4" />
                        </svg>
                    </div>
                    <div>
                        <p class="font-extrabold leading-none tracking-tight text-[#2d241d]">SAP.HRIS</p>
                        <p class="text-xs text-[#6f5c4b] mt-1">Sawita Group</p>
                    </div>
                </div>

                <div class="max-w-md">
                    <p class="text-[10px] font-bold uppercase tracking-[0.24em] theme-accent mb-6">Human resources information system</p>
                    <div class="float-logo mb-8 flex h-24 w-24 items-center justify-center">
                        <img src="{{ asset('images/pertamina-logo.svg') }}" alt="Pertamina" class="h-20 w-20 object-contain">
                    </div>
                    <h1 class="text-3xl md:text-4xl font-extrabold leading-relaxed tracking-tight mb-4 text-[#2d241d]">Semua tim,<br><span class="text-[#5b4835]">satu kendali.</span></h1>
                    <p class="text-[#5f5148] text-sm leading-relaxed max-w-sm">Satu sistem untuk mengelola operasional dan kehadiran karyawan secara teratur.</p>
                </div>
            </div>

            <div class="relative z-10 mt-10 text-xs text-[#66594c]">
                <span>&copy; {{ date('Y') }} SAP.HRIS</span>
            </div>
        </div>

        <div class="p-8 md:p-16 flex flex-col justify-center bg-gradient-to-br from-[#fffdf9] via-[#fffcf7] to-[#f7f0e4]">
            <div class="mb-8">
                <p class="text-xs font-extrabold uppercase tracking-[0.2em] theme-accent mb-3">Masuk</p>
                <h2 class="text-3xl font-extrabold tracking-tight text-[#2d241d] mb-2">Selamat datang</h2>
                <p class="login-subtitle text-sm">Gunakan akun perusahaan untuk masuk ke dashboard.</p>
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
                    <label class="login-label block text-xs font-extrabold mb-2">Username</label>
                    <div class="field relative flex items-center rounded-xl border transition">
                        <svg xmlns="http://www.w3.org/2000/svg" class="field-icon w-4 h-4 absolute left-3.5 top-1/2 -translate-y-1/2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                        </svg>
                        <input type="text" name="username" value="{{ old('username') }}" required autofocus autocomplete="username" class="w-full bg-transparent pl-10 pr-3 py-3.5 text-sm focus:outline-none" placeholder="Masukkan username">
                    </div>
                </div>

                <div>
                    <label class="login-label block text-xs font-extrabold mb-2">Password</label>
                    <div class="field relative flex items-center rounded-xl border transition">
                        <svg xmlns="http://www.w3.org/2000/svg" class="field-icon w-4 h-4 absolute left-3.5 top-1/2 -translate-y-1/2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" />
                        </svg>
                        <input id="password" type="password" name="password" required autocomplete="current-password" class="w-full bg-transparent pl-10 pr-11 py-3.5 text-sm focus:outline-none" placeholder="Masukkan password">
                        <button type="button" id="togglePassword" class="toggle-password absolute right-3 top-1/2 -translate-y-1/2 p-1" aria-label="Lihat password">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.576 3.01 9.964 7.183a1.012 1.012 0 010 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.576-3.01-9.964-7.183z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                        </button>
                    </div>
                </div>

                <div class="flex items-center justify-between gap-3 pt-1">
                    <label class="inline-flex items-center gap-2 text-xs text-[#5f5148]">
                        <input type="checkbox" class="h-4 w-4 rounded border-[#c7b190] bg-transparent text-[#c49c5a] focus:ring-[#c49c5a]">
                        Ingat saya
                    </label>
                    <a href="{{ route('password.request') }}" class="login-link text-xs font-semibold">Lupa password?</a>
                </div>

                <button type="submit"
                        class="premium-button w-full text-[#07152e] font-extrabold py-3.5 rounded-xl transition-all duration-200 flex items-center justify-center gap-2">
                    Masuk ke dashboard
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" />
                    </svg>
                </button>
            </form>

            <script>
                document.addEventListener('DOMContentLoaded', function () {
                    const passwordInput = document.getElementById('password');
                    const togglePasswordButton = document.getElementById('togglePassword');

                    if (passwordInput && togglePasswordButton) {
                        togglePasswordButton.addEventListener('click', function () {
                            const isPassword = passwordInput.type === 'password';
                            passwordInput.type = isPassword ? 'text' : 'password';
                            togglePasswordButton.setAttribute('aria-label', isPassword ? 'Sembunyikan password' : 'Lihat password');
                            togglePasswordButton.innerHTML = isPassword ? `
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.5a10.529 10.529 0 01-4.293 5.773M9.88 9.88a3 3 0 104.24 4.24M3 3l18 18" />
                                </svg>
                            ` : `
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.576 3.01 9.964 7.183a1.012 1.012 0 010 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.576-3.01-9.964-7.183z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                            `;
                        });
                    }
                });
            </script>

            <div class="mt-5 flex justify-between text-xs font-semibold">
                <span class="text-[#7b6d62]">Baru di SAP.HRIS?</span>
                <a href="{{ route('register') }}" class="login-link">Buat akun baru</a>
            </div>

        </div>
    </div>

</body>
</html>