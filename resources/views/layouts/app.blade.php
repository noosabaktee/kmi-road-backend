<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'KMI Road - Form Pengajuan Kendaraan Dinas') | Kalbe Nutritionals</title>

    <!-- Google Fonts: Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- FontAwesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- TailwindCSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'sans-serif'],
                    },
                    colors: {
                        kalbe: {
                            50: '#f0fdf4',
                            100: '#dcfce7',
                            200: '#bbf7d0',
                            300: '#86efac',
                            400: '#4ade80',
                            500: '#008542', // Kalbe Primary Emerald Green
                            600: '#007038',
                            700: '#00592c',
                            800: '#064e2b',
                            900: '#053d22',
                            lime: '#84BD00', // Kalbe Eco Lime Accent
                            limeHover: '#73a700',
                        }
                    }
                }
            }
        }
    </script>
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #f8fafc;
        }

        .kalbe-gradient {
            background: linear-gradient(135deg, #008542 0%, #064e2b 100%);
        }

        .glass-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(0, 133, 66, 0.1);
        }
    </style>
    @stack('styles')
</head>

<body class="min-h-screen flex flex-col text-slate-800 antialiased selection:bg-kalbe-500 selection:text-white">

    <!-- Top Navigation Bar -->
    <header class="sticky top-0 z-40 bg-white/95 backdrop-blur-md border-b border-slate-200/80 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 sm:h-20 flex items-center justify-between gap-2">
            <!-- Brand Logo -->
            <a href="{{ route('employee.form') }}" class="flex items-center space-x-2.5 sm:space-x-3 group min-w-0">
                <div class="w-9 h-9 sm:w-10 sm:h-10 rounded-xl kalbe-gradient flex items-center justify-center text-white shadow-md shadow-kalbe-500/20 group-hover:scale-105 transition-transform flex-shrink-0">
                    <i class="fa-solid fa-car-side text-base sm:text-lg"></i>
                </div>
                <div class="min-w-0">
                    <div class="flex items-center space-x-1.5 sm:space-x-2">
                        <span class="font-extrabold text-base sm:text-xl tracking-tight text-slate-900">KMI <span class="text-kalbe-500">ROAD</span></span>
                        <span class="px-1.5 sm:px-2 py-0.5 text-[9px] sm:text-[10px] font-bold uppercase tracking-wider bg-kalbe-50 text-kalbe-600 rounded-full border border-kalbe-200 flex-shrink-0">Dinas</span>
                    </div>
                    <p class="text-[10px] sm:text-[11px] font-medium text-slate-500 -mt-0.5 truncate hidden sm:block">PT Sanghiang Perkasa • Kalbe Nutritionals</p>
                </div>
            </a>

            <!-- Desktop Menu -->
            <div class="hidden md:flex items-center space-x-2.5">
                <a href="{{ route('employee.form') }}" class="inline-flex items-center space-x-1.5 px-3.5 py-2 text-xs font-semibold rounded-xl {{ request()->routeIs('employee.form') ? 'text-kalbe-700 bg-kalbe-50 font-bold border border-kalbe-200' : 'text-slate-700 hover:bg-slate-100' }} transition-colors">
                    <i class="fa-solid fa-file-pen {{ request()->routeIs('employee.form') ? 'text-kalbe-600' : 'text-slate-400' }}"></i>
                    <span>Form Pengajuan</span>
                </a>
                <a href="{{ route('employee.status') }}" class="inline-flex items-center space-x-1.5 px-3.5 py-2 text-xs font-semibold rounded-xl {{ request()->routeIs('employee.status') ? 'text-kalbe-700 bg-kalbe-50 font-bold border border-kalbe-200' : 'text-slate-700 bg-slate-100 hover:bg-slate-200' }} transition-colors">
                    <i class="fa-solid fa-magnifying-glass {{ request()->routeIs('employee.status') ? 'text-kalbe-600' : 'text-slate-500' }}"></i>
                    <span>Cek Status Tiket</span>
                </a>
                <a href="{{ route('admin.login') }}" class="inline-flex items-center space-x-1.5 px-3.5 py-2 text-xs font-semibold rounded-xl text-white kalbe-gradient hover:opacity-95 transition-opacity shadow-sm shadow-kalbe-500/30">
                    <i class="fa-solid fa-shield-halved"></i>
                    <span>Portal Admin HC</span>
                </a>
            </div>

            <!-- Mobile Quick Actions & Hamburger Menu Button -->
            <div class="flex md:hidden items-center space-x-1.5 sm:space-x-2">
                <a href="{{ route('employee.status') }}" class="inline-flex items-center space-x-1 px-2.5 py-1.5 text-xs font-bold rounded-lg {{ request()->routeIs('employee.status') ? 'text-kalbe-700 bg-kalbe-50 border border-kalbe-200' : 'text-slate-700 bg-slate-100 hover:bg-slate-200' }}">
                    <i class="fa-solid fa-magnifying-glass text-slate-500 text-xs"></i>
                    <span class="text-[11px]">Cek Status</span>
                </a>

                <button type="button" id="mobileMenuBtn" onclick="toggleMobileMenu()" class="w-9 h-9 rounded-lg bg-slate-100 text-slate-700 flex items-center justify-center hover:bg-slate-200 focus:outline-none transition-colors" aria-label="Toggle Menu">
                    <i id="mobileMenuIcon" class="fa-solid fa-bars text-sm"></i>
                </button>
            </div>
        </div>

        <!-- Mobile Dropdown Navigation Menu -->
        <div id="mobileMenu" class="hidden md:hidden border-t border-slate-200 bg-white/95 backdrop-blur-md px-4 py-3 space-y-2 shadow-lg">
            <a href="{{ route('employee.form') }}" class="flex items-center justify-between p-2.5 rounded-xl text-xs font-bold {{ request()->routeIs('employee.form') ? 'text-kalbe-700 bg-kalbe-50 border border-kalbe-200' : 'text-slate-700 hover:bg-slate-50' }}">
                <div class="flex items-center space-x-2.5">
                    <i class="fa-solid fa-file-pen text-kalbe-600 w-4 text-center"></i>
                    <span>Formulir Pengajuan Dinas</span>
                </div>
                @if(request()->routeIs('employee.form'))
                <span class="w-2 h-2 rounded-full bg-kalbe-500"></span>
                @endif
            </a>
            <a href="{{ route('employee.status') }}" class="flex items-center justify-between p-2.5 rounded-xl text-xs font-bold {{ request()->routeIs('employee.status') ? 'text-kalbe-700 bg-kalbe-50 border border-kalbe-200' : 'text-slate-700 hover:bg-slate-50' }}">
                <div class="flex items-center space-x-2.5">
                    <i class="fa-solid fa-magnifying-glass text-slate-500 w-4 text-center"></i>
                    <span>Cek Status / Lacak Tiket</span>
                </div>
                @if(request()->routeIs('employee.status'))
                <span class="w-2 h-2 rounded-full bg-kalbe-500"></span>
                @endif
            </a>
            <div class="pt-2 border-t border-slate-100">
                <a href="{{ route('admin.login') }}" class="w-full flex items-center justify-center space-x-2 py-2.5 px-4 rounded-xl text-xs font-bold text-white kalbe-gradient shadow-md shadow-kalbe-500/20">
                    <i class="fa-solid fa-shield-halved"></i>
                    <span>Masuk ke Portal Admin HC</span>
                </a>
            </div>
        </div>
    </header>

    <!-- Main Content Area -->
    <main class="flex-1">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-white border-t border-slate-200 py-6 mt-12 text-center text-xs text-slate-500">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col sm:flex-row items-center justify-between gap-2">
            <div class="flex flex-wrap items-center justify-center space-x-2">
                <span class="font-bold text-slate-700">KMI Road</span>
                <span>•</span>
                <span>Vehicle & Business Trip Monitoring System</span>
            </div>
            <p>&copy; {{ date('Y') }} PT Sanghiang Perkasa (Kalbe Nutritionals). All rights reserved.</p>
        </div>
    </footer>

    <script>
        function toggleMobileMenu() {
            const menu = document.getElementById('mobileMenu');
            const icon = document.getElementById('mobileMenuIcon');
            if (menu.classList.contains('hidden')) {
                menu.classList.remove('hidden');
                icon.classList.remove('fa-bars');
                icon.classList.add('fa-xmark');
            } else {
                menu.classList.add('hidden');
                icon.classList.remove('fa-xmark');
                icon.classList.add('fa-bars');
            }
        }
    </script>
    @stack('scripts')
</body>

</html>