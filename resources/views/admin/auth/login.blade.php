<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin HC | KMI Road - Kalbe Nutritionals</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- FontAwesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <!-- TailwindCSS -->
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
                            500: '#008542',
                            600: '#007038',
                            700: '#00592c',
                            800: '#064e2b',
                            lime: '#84BD00',
                        }
                    }
                }
            }
        }
    </script>
    <style>
        .kalbe-gradient {
            background: linear-gradient(135deg, #008542 0%, #064e2b 100%);
        }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center bg-slate-100 p-4 font-sans text-slate-800 antialiased">
    <div class="max-w-md w-full">
        <!-- Logo & Title -->
        <div class="text-center mb-8">
            <div class="inline-flex w-16 h-16 rounded-2xl kalbe-gradient items-center justify-center text-white text-2xl shadow-xl shadow-kalbe-500/20 mb-4">
                <i class="fa-solid fa-car-side"></i>
            </div>
            <h1 class="text-2xl font-extrabold tracking-tight text-slate-900">KMI <span class="text-kalbe-500">ROAD</span></h1>
            <p class="text-xs font-bold uppercase tracking-wider text-slate-500 mt-0.5">Portal Admin Human Capital (HC)</p>
            <p class="text-[11px] text-slate-400">PT Sanghiang Perkasa • Kalbe Nutritionals</p>
        </div>

        <!-- Login Card -->
        <div class="bg-white rounded-3xl p-8 shadow-xl shadow-slate-200/60 border border-slate-200/80">
            @if ($errors->any())
                <div class="mb-6 p-3.5 rounded-xl bg-red-50 border border-red-200 text-red-800 text-xs font-medium flex items-center space-x-2">
                    <i class="fa-solid fa-circle-exclamation text-red-600"></i>
                    <span>{{ $errors->first() }}</span>
                </div>
            @endif

            <form action="{{ route('admin.login.submit') }}" method="POST" class="space-y-5">
                @csrf

                <div>
                    <label for="txtEmail" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Email HC</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-slate-400"><i class="fa-solid fa-envelope"></i></span>
                        <input type="email" name="txtEmail" id="txtEmail" value="{{ old('txtEmail', 'admin@kmi.kalbe.co.id') }}" required placeholder="admin@kmi.kalbe.co.id"
                            class="w-full pl-10 pr-4 py-3 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-kalbe-500 focus:border-transparent transition-all">
                    </div>
                </div>

                <div>
                    <label for="password" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Kata Sandi</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-slate-400"><i class="fa-solid fa-lock"></i></span>
                        <input type="password" name="password" id="password" value="admin123" required placeholder="••••••••"
                            class="w-full pl-10 pr-4 py-3 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-kalbe-500 focus:border-transparent transition-all">
                    </div>
                </div>

                <div class="flex items-center justify-between text-xs">
                    <label class="flex items-center space-x-2 cursor-pointer">
                        <input type="checkbox" name="remember" class="w-4 h-4 text-kalbe-600 rounded border-slate-300 focus:ring-kalbe-500">
                        <span class="text-slate-600">Ingat Saya</span>
                    </label>
                </div>

                <button type="submit" class="w-full py-3.5 rounded-xl kalbe-gradient text-white font-bold text-sm shadow-lg shadow-kalbe-500/30 hover:opacity-95 transition-all flex items-center justify-center space-x-2">
                    <i class="fa-solid fa-arrow-right-to-bracket"></i>
                    <span>Masuk ke Dashboard</span>
                </button>
            </form>
        </div>

        <div class="text-center mt-6">
            <a href="{{ route('employee.form') }}" class="text-xs font-semibold text-kalbe-600 hover:text-kalbe-700">
                <i class="fa-solid fa-arrow-left mr-1"></i> Kembali ke Form Dinas Karyawan
            </a>
        </div>
    </div>
</body>
</html>
