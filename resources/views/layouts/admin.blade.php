<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin HC Dashboard') | KMI Road - Kalbe Nutritionals</title>

    <!-- Google Fonts: Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- FontAwesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- Leaflet Map CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />

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

        .sidebar-active {
            background: rgba(0, 133, 66, 0.12);
            color: #008542;
            font-weight: 700;
            border-right: 4px solid #008542;
        }

        /* Radar pulse for active tracking */
        @keyframes pulse-ring {
            0% {
                transform: scale(0.95);
                opacity: 0.8;
            }

            50% {
                transform: scale(1.3);
                opacity: 0.2;
            }

            100% {
                transform: scale(0.95);
                opacity: 0.8;
            }
        }

        .radar-pulse {
            animation: pulse-ring 2s cubic-bezier(0.455, 0.03, 0.515, 0.955) infinite;
        }
    </style>
    @stack('styles')
</head>

<body class="min-h-screen bg-slate-100 flex text-slate-800 antialiased selection:bg-kalbe-500 selection:text-white relative">

    <!-- Mobile Sidebar Backdrop -->
    <div id="sidebarBackdrop" onclick="closeSidebar()" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-40 hidden lg:hidden transition-opacity"></div>

    <!-- Sidebar Navigation Drawer -->
    <aside id="adminSidebar" class="w-64 bg-white border-r border-slate-200 flex flex-col fixed inset-y-0 left-0 z-50 transform -translate-x-full lg:translate-x-0 transition-transform duration-300 ease-in-out shadow-2xl lg:shadow-none">
        <!-- Brand Header -->
        <div class="h-16 sm:h-20 px-5 sm:px-6 border-b border-slate-100 flex items-center justify-between">
            <a href="{{ route('admin.dashboard') }}" class="flex items-center space-x-3">
                <div class="w-9 h-9 sm:w-10 sm:h-10 rounded-xl kalbe-gradient flex items-center justify-center text-white shadow-md shadow-kalbe-500/20">
                    <i class="fa-solid fa-car-side text-base sm:text-lg"></i>
                </div>
                <div>
                    <span class="font-extrabold text-lg sm:text-xl tracking-tight text-slate-900">KMI <span class="text-kalbe-500">ROAD</span></span>
                    <p class="text-[9px] sm:text-[10px] font-bold tracking-wider uppercase text-kalbe-600">Admin HC Portal</p>
                </div>
            </a>
            <button type="button" onclick="closeSidebar()" class="lg:hidden p-2 text-slate-400 hover:text-slate-600 hover:bg-slate-50 rounded-lg" title="Tutup Menu">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>
        </div>

        <!-- Navigation Links -->
        <div class="flex-1 py-5 px-3 space-y-1.5 overflow-y-auto">
            <div class="px-3 pb-2 text-[10px] font-bold uppercase tracking-wider text-slate-400">Monitoring & Fleet</div>

            <a href="{{ route('admin.dashboard') }}" class="flex items-center space-x-3 px-3.5 py-2.5 rounded-xl text-sm font-medium transition-colors {{ request()->routeIs('admin.dashboard*') ? 'sidebar-active' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                <i class="fa-solid fa-gauge-high w-5 text-center text-base {{ request()->routeIs('admin.dashboard*') ? 'text-kalbe-500' : 'text-slate-400' }}"></i>
                <span>Dashboard</span>
            </a>

            <a href="{{ route('admin.tracking') }}" class="flex items-center justify-between px-3.5 py-2.5 rounded-xl text-sm font-medium transition-colors {{ request()->routeIs('admin.tracking*') ? 'sidebar-active' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                <div class="flex items-center space-x-3">
                    <i class="fa-solid fa-map-location-dot w-5 text-center text-base {{ request()->routeIs('admin.tracking*') ? 'text-kalbe-500' : 'text-slate-400' }}"></i>
                    <span>Live Tracking GPS</span>
                </div>
                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-700 animate-pulse">
                    LIVE
                </span>
            </a>

            <a href="{{ route('admin.trips.index') }}" class="flex items-center justify-between px-3.5 py-2.5 rounded-xl text-sm font-medium transition-colors {{ request()->routeIs('admin.trips*') ? 'sidebar-active' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                <div class="flex items-center space-x-3">
                    <i class="fa-solid fa-route w-5 text-center text-base {{ request()->routeIs('admin.trips*') ? 'text-kalbe-500' : 'text-slate-400' }}"></i>
                    <span>Jadwal & Dinas</span>
                </div>
            </a>

            <div class="pt-6 px-3 pb-2 text-[10px] font-bold uppercase tracking-wider text-slate-400">Master Data</div>

            <a href="{{ route('admin.vehicles.index') }}" class="flex items-center space-x-3 px-3.5 py-2.5 rounded-xl text-sm font-medium transition-colors {{ request()->routeIs('admin.vehicles*') ? 'sidebar-active' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                <i class="fa-solid fa-van-shuttle w-5 text-center text-base {{ request()->routeIs('admin.vehicles*') ? 'text-kalbe-500' : 'text-slate-400' }}"></i>
                <span>Kendaraan Dinas</span>
            </a>

            <a href="{{ route('admin.drivers.index') }}" class="flex items-center space-x-3 px-3.5 py-2.5 rounded-xl text-sm font-medium transition-colors {{ request()->routeIs('admin.drivers*') ? 'sidebar-active' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                <i class="fa-solid fa-id-card-clip w-5 text-center text-base {{ request()->routeIs('admin.drivers*') ? 'text-kalbe-500' : 'text-slate-400' }}"></i>
                <span>Driver (Supir)</span>
            </a>

            <div class="pt-6 px-3 pb-2 text-[10px] font-bold uppercase tracking-wider text-slate-400">Laporan & Audit</div>

            <a href="{{ route('admin.reports.index') }}" class="flex items-center space-x-3 px-3.5 py-2.5 rounded-xl text-sm font-medium transition-colors {{ request()->routeIs('admin.reports*') ? 'sidebar-active' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                <i class="fa-solid fa-file-invoice-dollar w-5 text-center text-base {{ request()->routeIs('admin.reports*') ? 'text-kalbe-500' : 'text-slate-400' }}"></i>
                <span>Laporan & BBM</span>
            </a>

            <div class="pt-6 px-3 pb-2 text-[10px] font-bold uppercase tracking-wider text-slate-400">Quick Access</div>

            <a href="{{ route('employee.form') }}" target="_blank" class="flex items-center space-x-3 px-3.5 py-2.5 rounded-xl text-sm font-medium text-slate-600 hover:bg-slate-50 hover:text-slate-900">
                <i class="fa-solid fa-arrow-up-right-from-square w-5 text-center text-slate-400"></i>
                <span>Buka Form Karyawan</span>
            </a>
        </div>

        <!-- User Profile & Logout -->
        <div class="p-4 border-t border-slate-100 bg-slate-50/50">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-3 overflow-hidden">
                    <div class="w-9 h-9 rounded-full kalbe-gradient flex items-center justify-center text-white font-bold text-sm flex-shrink-0">
                        {{ strtoupper(substr(Auth::user()->txtUserName ?? 'HC', 0, 2)) }}
                    </div>
                    <div class="truncate">
                        <p class="text-xs font-bold text-slate-900 truncate">{{ Auth::user()->txtUserName ?? 'Admin HC' }}</p>
                        <p class="text-[10px] text-slate-500 truncate">{{ Auth::user()->txtEmail ?? 'admin@kmi.kalbe.co.id' }}</p>
                    </div>
                </div>
                <form action="{{ route('admin.logout') }}" method="POST">
                    @csrf
                    <button type="submit" title="Keluar / Logout" class="p-2 rounded-lg text-slate-400 hover:text-red-600 hover:bg-red-50 transition-colors">
                        <i class="fa-solid fa-right-from-bracket"></i>
                    </button>
                </form>
            </div>
        </div>
    </aside>

    <!-- Main Content Wrapper -->
    <div class="flex-1 ml-0 lg:ml-64 flex flex-col min-h-screen w-full transition-all duration-300">
        <!-- Top Nav Bar -->
        <header class="h-16 sm:h-20 bg-white border-b border-slate-200/80 px-4 sm:px-6 lg:px-8 flex items-center justify-between sticky top-0 z-30 shadow-xs">
            <div class="flex items-center space-x-3 sm:space-x-4">
                <!-- Hamburger Button for Mobile -->
                <button type="button" onclick="openSidebar()" class="lg:hidden p-2 rounded-xl text-slate-600 hover:bg-slate-100 hover:text-slate-900 focus:outline-none" title="Buka Menu">
                    <i class="fa-solid fa-bars text-lg"></i>
                </button>
                <div>
                    <h1 class="text-sm sm:text-base lg:text-lg font-extrabold text-slate-900 truncate max-w-[180px] sm:max-w-md">@yield('header_title', 'Dashboard Monitoring')</h1>
                </div>
            </div>

            <div class="flex items-center space-x-2 sm:space-x-4">
                <a href="{{ route('admin.trips.create') }}" class="inline-flex items-center space-x-1.5 sm:space-x-2 px-3 sm:px-4 py-2 text-xs font-bold rounded-xl text-white kalbe-gradient shadow-md shadow-kalbe-500/20 hover:opacity-95 transition-opacity">
                    <i class="fa-solid fa-plus text-xs"></i>
                    <span class="hidden sm:inline">Buat Jadwal Dinas</span>
                    <span class="sm:hidden">Buat Jadwal</span>
                </a>
            </div>
        </header>

        <!-- Flash Messages -->
        @if (session('success'))
        <div class="mx-4 sm:mx-6 lg:mx-8 mt-4 sm:mt-6 p-3.5 sm:p-4 rounded-2xl bg-emerald-50 border border-emerald-200 flex items-center space-x-3 text-emerald-800 text-xs sm:text-sm animate-fade-in shadow-xs">
            <i class="fa-solid fa-circle-check text-emerald-600 text-base sm:text-lg flex-shrink-0"></i>
            <span class="font-medium">{{ session('success') }}</span>
        </div>
        @endif

        @if ($errors->any())
        <div class="mx-4 sm:mx-6 lg:mx-8 mt-4 sm:mt-6 p-3.5 sm:p-4 rounded-2xl bg-red-50 border border-red-200 flex items-start space-x-3 text-red-800 text-xs sm:text-sm animate-fade-in shadow-xs">
            <i class="fa-solid fa-circle-exclamation text-red-600 text-base sm:text-lg mt-0.5 flex-shrink-0"></i>
            <div class="space-y-1">
                @foreach ($errors->all() as $error)
                <p class="font-medium">{{ $error }}</p>
                @endforeach
            </div>
        </div>
        @endif

        <!-- Main Page Content -->
        <main class="flex-1 p-4 sm:p-6 lg:p-8">
            @yield('content')
        </main>
    </div>

    <!-- Leaflet JS -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

    <!-- Global Mobile Sidebar Script -->
    <script>
        function openSidebar() {
            const sidebar = document.getElementById('adminSidebar');
            const backdrop = document.getElementById('sidebarBackdrop');
            if (sidebar) sidebar.classList.remove('-translate-x-full');
            if (backdrop) backdrop.classList.remove('hidden');
            document.body.classList.add('overflow-hidden', 'lg:overflow-auto');
        }

        function closeSidebar() {
            const sidebar = document.getElementById('adminSidebar');
            const backdrop = document.getElementById('sidebarBackdrop');
            if (sidebar) sidebar.classList.add('-translate-x-full');
            if (backdrop) backdrop.classList.add('hidden');
            document.body.classList.remove('overflow-hidden', 'lg:overflow-auto');
        }
    </script>
    @stack('scripts')
</body>

</html>