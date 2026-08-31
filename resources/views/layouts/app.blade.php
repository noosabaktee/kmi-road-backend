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
    <header class="sticky top-0 z-40 bg-white/90 backdrop-blur-md border-b border-slate-200/80 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
            <a href="{{ route('employee.form') }}" class="flex items-center space-x-3 group">
                <div class="w-10 h-10 rounded-xl kalbe-gradient flex items-center justify-center text-white shadow-md shadow-kalbe-500/20 group-hover:scale-105 transition-transform">
                    <i class="fa-solid fa-car-side text-lg"></i>
                </div>
                <div>
                    <div class="flex items-center space-x-2">
                        <span class="font-extrabold text-xl tracking-tight text-slate-900">KMI <span class="text-kalbe-500">ROAD</span></span>
                        <span class="px-2 py-0.5 text-[10px] font-bold uppercase tracking-wider bg-kalbe-50 text-kalbe-600 rounded-full border border-kalbe-200">Dinas</span>
                    </div>
                    <p class="text-[11px] font-medium text-slate-500 -mt-1">PT Sanghiang Perkasa • Kalbe Nutritionals</p>
                </div>
            </a>

            <div class="flex items-center space-x-3">
                <a href="{{ route('employee.status') }}" class="inline-flex items-center space-x-1.5 px-3.5 py-2 text-xs font-semibold rounded-lg text-slate-700 bg-slate-100 hover:bg-slate-200 transition-colors">
                    <i class="fa-solid fa-magnifying-glass text-slate-500"></i>
                    <span>Cek Status Tiket</span>
                </a>
                <a href="{{ route('admin.login') }}" class="inline-flex items-center space-x-1.5 px-3.5 py-2 text-xs font-semibold rounded-lg text-white kalbe-gradient hover:opacity-95 transition-opacity shadow-sm shadow-kalbe-500/30">
                    <i class="fa-solid fa-shield-halved"></i>
                    <span>Portal Admin HC</span>
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
        <div class="max-w-7xl mx-auto px-4 flex flex-col sm:flex-row items-center justify-between gap-2">
            <div class="flex items-center space-x-2">
                <span class="font-bold text-slate-700">KMI Road</span>
                <span>•</span>
                <span>Vehicle & Business Trip Monitoring System</span>
            </div>
            <p>&copy; {{ date('Y') }} PT Sanghiang Perkasa (Kalbe Nutritionals). All rights reserved.</p>
        </div>
    </footer>

    @stack('scripts')
</body>
</html>
