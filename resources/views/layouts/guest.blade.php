<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'FORTA - Form & Registrasi Terpadu Pradita')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-white font-sans antialiased text-slate-800 min-h-screen flex flex-col selection:bg-teal-600 selection:text-white" x-data="{ mobileMenuOpen: false }">
    
    <!-- Header -->
    <header class="bg-white border-b border-slate-200 sticky top-0 z-50 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-20">
                <!-- Logo -->
                <div class="flex items-center gap-3">
                    <img src="https://pradita.ac.id/assets/img/logo-pradita.png" alt="FORTA Logo" class="h-10 w-auto object-contain">
                    <div class="hidden sm:block border-l border-slate-300 pl-3">
                        <h1 class="text-xl font-bold text-slate-900 leading-none">FORTA</h1>
                        <p class="text-[10px] uppercase font-semibold tracking-wider text-slate-500 mt-0.5">Form & Registrasi Terpadu</p>
                    </div>
                </div>

                <!-- Desktop Navigation -->
                <nav class="hidden md:flex items-center space-x-8">
                    <a href="{{ route('home') }}" class="text-sm font-medium text-teal-700 border-b-2 border-teal-600 py-7">Beranda</a>
                    <a href="#form-akademik" class="text-sm font-medium text-slate-600 hover:text-teal-600 py-7 transition-colors">Form Akademik</a>
                    <a href="#kalender-akademik" class="text-sm font-medium text-slate-600 hover:text-teal-600 py-7 transition-colors">Kalender Akademik</a>
                </nav>

                <!-- Auth Buttons Desktop -->
                <div class="hidden md:flex items-center space-x-4">
                    <a href="{{ route('admin.login') }}" class="text-sm font-medium text-slate-600 hover:text-slate-900 transition-colors">Masuk Admin</a>
                    <a href="{{ route('student.login') }}" class="inline-flex items-center justify-center px-5 py-2 text-sm font-semibold text-white transition-colors bg-teal-600 rounded-md hover:bg-teal-700 shadow-sm">
                        Masuk Mahasiswa
                    </a>
                </div>

                <!-- Mobile Menu Button -->
                <div class="flex items-center md:hidden">
                    <button @click="mobileMenuOpen = !mobileMenuOpen" type="button" class="text-slate-600 hover:text-slate-900 focus:outline-none p-2 rounded-md hover:bg-slate-100" aria-label="Toggle menu">
                        <svg x-show="!mobileMenuOpen" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                        </svg>
                        <svg x-show="mobileMenuOpen" style="display: none;" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- Mobile Drawer -->
        <div x-show="mobileMenuOpen" style="display: none;" @click.away="mobileMenuOpen = false" class="md:hidden absolute top-20 left-0 w-full bg-white border-b border-slate-200 shadow-lg z-40">
            <div class="px-4 pt-2 pb-6 space-y-1">
                <a href="{{ route('home') }}" @click="mobileMenuOpen = false" class="block px-3 py-3 rounded-md text-base font-medium text-teal-700 bg-teal-50">Beranda</a>
                <a href="#form-akademik" @click="mobileMenuOpen = false" class="block px-3 py-3 rounded-md text-base font-medium text-slate-700 hover:bg-slate-50 hover:text-teal-600">Form Akademik</a>
                <a href="#kalender-akademik" @click="mobileMenuOpen = false" class="block px-3 py-3 rounded-md text-base font-medium text-slate-700 hover:bg-slate-50 hover:text-teal-600">Kalender Akademik</a>
                
                <div class="mt-4 pt-4 border-t border-slate-200 flex flex-col gap-3">
                    <a href="{{ route('student.login') }}" class="w-full inline-flex items-center justify-center px-4 py-2.5 text-base font-semibold text-white bg-teal-600 rounded-md hover:bg-teal-700">Masuk Mahasiswa</a>
                    <a href="{{ route('admin.login') }}" class="w-full inline-flex items-center justify-center px-4 py-2.5 text-base font-semibold text-slate-700 bg-white border border-slate-300 rounded-md hover:bg-slate-50">Masuk Admin</a>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="flex-grow flex flex-col">
        @yield('content')
    </main>
    
    <!-- Footer -->
    <footer class="bg-slate-900 border-t border-slate-800 mt-auto text-slate-400">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <div class="md:col-span-2">
                    <div class="flex items-center gap-3 mb-4">
                        <img src="https://pradita.ac.id/assets/img/logo-pradita.png" alt="FORTA Logo" class="h-10 w-auto object-contain brightness-0 invert">
                        <div class="border-l border-slate-600 pl-3">
                            <h2 class="text-xl font-bold text-white leading-none">FORTA</h2>
                        </div>
                    </div>
                    <p class="text-sm leading-relaxed mb-4 text-slate-400 max-w-sm">
                        Sistem Form & Registrasi Terpadu Pradita.<br>
                        Program Studi S1 Informatika,<br>Universitas Pradita.
                    </p>
                </div>
                
                <div>
                    <h3 class="text-sm font-semibold text-white tracking-wider uppercase mb-4">Tautan Cepat</h3>
                    <ul class="space-y-3 text-sm">
                        <li><a href="#form-akademik" class="hover:text-teal-400 transition-colors">Form Akademik Aktif</a></li>
                        <li><a href="#kalender-akademik" class="hover:text-teal-400 transition-colors">Kalender Akademik</a></li>
                        <li><a href="#cara-menggunakan" class="hover:text-teal-400 transition-colors">Cara Menggunakan</a></li>
                    </ul>
                </div>
                
                <div>
                    <h3 class="text-sm font-semibold text-white tracking-wider uppercase mb-4">Akses Sistem</h3>
                    <ul class="space-y-3 text-sm">
                        <li><a href="{{ route('student.login') }}" class="hover:text-teal-400 transition-colors">Login Mahasiswa (SSO)</a></li>
                        <li><a href="{{ route('admin.login') }}" class="hover:text-teal-400 transition-colors">Login Administrator</a></li>
                    </ul>
                </div>
            </div>
            <div class="mt-12 pt-8 border-t border-slate-800 text-sm text-center md:text-left flex flex-col md:flex-row justify-between items-center gap-4">
                <p>&copy; {{ date('Y') }} S1 Informatika Universitas Pradita. All rights reserved.</p>
            </div>
        </div>
    </footer>
</body>
</html>
