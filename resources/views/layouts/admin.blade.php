<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Dashboard') - FORTA</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="bg-gray-100 font-sans leading-normal tracking-normal flex h-screen overflow-hidden">

    <!-- Sidebar -->
    <div class="bg-gray-800 shadow-xl h-screen w-64 flex flex-col transition-all duration-300 z-10" id="sidebar">
        <div class="flex items-center justify-center h-16 border-b border-gray-700">
            <span class="text-white font-bold text-lg uppercase">FORTA Admin</span>
        </div>
        <div class="overflow-y-auto overflow-x-hidden flex-grow">
            <ul class="flex flex-col py-4 space-y-1">
                <li class="px-5">
                    <div class="flex flex-row items-center h-8">
                        <div class="text-sm font-light tracking-wide text-gray-400">Menu</div>
                    </div>
                </li>
                <li>
                    <a href="{{ route('admin.dashboard') }}" class="relative flex flex-row items-center h-11 focus:outline-none hover:bg-gray-700 text-gray-300 hover:text-white border-l-4 border-transparent hover:border-blue-500 pr-6">
                        <span class="inline-flex justify-center items-center ml-4">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                        </span>
                        <span class="ml-2 text-sm tracking-wide truncate">Dashboard</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.activity-types') }}" class="relative flex flex-row items-center h-11 focus:outline-none hover:bg-gray-700 text-gray-300 hover:text-white border-l-4 border-transparent hover:border-blue-500 pr-6">
                        <span class="inline-flex justify-center items-center ml-4">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path></svg>
                        </span>
                        <span class="ml-2 text-sm tracking-wide truncate">Jenis Kegiatan</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.forms') }}" class="relative flex flex-row items-center h-11 focus:outline-none hover:bg-gray-700 text-gray-300 hover:text-white border-l-4 border-transparent hover:border-blue-500 pr-6">
                        <span class="inline-flex justify-center items-center ml-4">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        </span>
                        <span class="ml-2 text-sm tracking-wide truncate">Kelola Form</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.students') }}" class="relative flex flex-row items-center h-11 focus:outline-none hover:bg-gray-700 text-gray-300 hover:text-white border-l-4 border-transparent hover:border-blue-500 pr-6">
                        <span class="inline-flex justify-center items-center ml-4">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                        </span>
                        <span class="ml-2 text-sm tracking-wide truncate">Data Mahasiswa</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.registrations') }}" class="relative flex flex-row items-center h-11 focus:outline-none hover:bg-gray-700 text-gray-300 hover:text-white border-l-4 border-transparent hover:border-blue-500 pr-6">
                        <span class="inline-flex justify-center items-center ml-4">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path></svg>
                        </span>
                        <span class="ml-2 text-sm tracking-wide truncate">Pendaftaran Mahasiswa</span>
                    </a>
                </li>
                <li x-data="{ open: {{ request()->routeIs('admin.internship-letters.*') || request()->routeIs('admin.signature-requests.*') || request()->routeIs('admin.signatories.*') ? 'true' : 'false' }} }">
                    <button @click="open = !open" class="w-full relative flex flex-row items-center h-11 focus:outline-none hover:bg-gray-700 text-gray-300 hover:text-white border-l-4 border-transparent hover:border-blue-500 pr-6 justify-between">
                        <div class="flex items-center">
                            <span class="inline-flex justify-center items-center ml-4">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                            </span>
                            <span class="ml-2 text-sm tracking-wide truncate">Pengajuan Surat</span>
                        </div>
                        <svg class="w-4 h-4 transition-transform duration-200" :class="{'transform rotate-180': open}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </button>
                    <ul x-show="open" class="bg-gray-900 py-2 space-y-1" style="display: none;">
                        <li>
                            <a href="{{ route('admin.internship-letters.index') }}" class="relative flex flex-row items-center h-10 focus:outline-none hover:bg-gray-700 text-gray-300 hover:text-white pl-12 pr-6">
                                <span class="text-sm tracking-wide truncate">Surat Magang/KP</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.internship-letters.settings') }}" class="relative flex flex-row items-center h-10 focus:outline-none hover:bg-gray-700 text-gray-300 hover:text-white pl-12 pr-6">
                                <span class="text-sm tracking-wide truncate">Template Surat Magang</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.signature-requests.index') ?? '#' }}" class="relative flex flex-row items-center h-10 focus:outline-none hover:bg-gray-700 text-gray-300 hover:text-white pl-12 pr-6">
                                <span class="text-sm tracking-wide truncate">Daftar Tanda Tangan</span>
                            </a>
                        </li>
                    </ul>
                </li>
                
                <!-- Manajemen Dokumen & Template (New) -->
                <li class="px-5 mt-4">
                    <div class="flex flex-row items-center h-8">
                        <div class="text-sm font-light tracking-wide text-gray-500 uppercase">Manajemen Dokumen</div>
                    </div>
                </li>
                <li x-data="{ open: {{ request()->routeIs('admin.document-templates.*') ? 'true' : 'false' }} }">
                    <button @click="open = !open" class="w-full relative flex flex-row items-center h-11 focus:outline-none hover:bg-gray-700 text-gray-300 hover:text-white border-l-4 border-transparent hover:border-blue-500 pr-6 justify-between">
                        <div class="flex items-center">
                            <span class="inline-flex justify-center items-center ml-4">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                            </span>
                            <span class="ml-2 text-sm tracking-wide truncate">Document Builder</span>
                        </div>
                        <svg class="w-4 h-4 transition-transform duration-200" :class="{'transform rotate-180': open}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </button>
                    <ul x-show="open" class="bg-gray-900 py-2 space-y-1" style="display: none;">
                        <li>
                            <a href="{{ route('admin.document-templates.index') }}" class="relative flex flex-row items-center h-10 focus:outline-none hover:bg-gray-700 text-gray-300 hover:text-white pl-12 pr-6">
                                <span class="text-sm tracking-wide truncate">Template Surat</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.document-templates.master-header') }}" class="relative flex flex-row items-center h-10 focus:outline-none hover:bg-gray-700 text-gray-300 hover:text-white pl-12 pr-6">
                                <span class="text-sm tracking-wide truncate">Kop Surat Master</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.document-templates.assets') }}" class="relative flex flex-row items-center h-10 focus:outline-none hover:bg-gray-700 text-gray-300 hover:text-white pl-12 pr-6">
                                <span class="text-sm tracking-wide truncate">Manajemen Aset</span>
                            </a>
                        </li>
                    </ul>
                </li>
                <li>
                    <a href="{{ route('admin.submissions') }}" class="relative flex flex-row items-center h-11 focus:outline-none hover:bg-gray-700 text-gray-300 hover:text-white border-l-4 border-transparent hover:border-blue-500 pr-6">
                        <span class="inline-flex justify-center items-center ml-4">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                        </span>
                        <span class="ml-2 text-sm tracking-wide truncate">Data Pengajuan</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.calendar') }}" class="relative flex flex-row items-center h-11 focus:outline-none hover:bg-gray-700 text-gray-300 hover:text-white border-l-4 border-transparent hover:border-blue-500 pr-6">
                        <span class="inline-flex justify-center items-center ml-4">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        </span>
                        <span class="ml-2 text-sm tracking-wide truncate">Kalender Akademik</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.email-blast') }}" class="relative flex flex-row items-center h-11 focus:outline-none hover:bg-gray-700 text-gray-300 hover:text-white border-l-4 border-transparent hover:border-blue-500 pr-6">
                        <span class="inline-flex justify-center items-center ml-4">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                        </span>
                        <span class="ml-2 text-sm tracking-wide truncate">Email Blast</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.users') }}" class="relative flex flex-row items-center h-11 focus:outline-none hover:bg-gray-700 text-gray-300 hover:text-white border-l-4 border-transparent hover:border-blue-500 pr-6">
                        <span class="inline-flex justify-center items-center ml-4">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                        </span>
                        <span class="ml-2 text-sm tracking-wide truncate">Manajemen Admin</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.lecturers') }}" class="relative flex flex-row items-center h-11 focus:outline-none hover:bg-gray-700 text-gray-300 hover:text-white border-l-4 border-transparent hover:border-blue-500 pr-6">
                        <span class="inline-flex justify-center items-center ml-4">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                        </span>
                        <span class="ml-2 text-sm tracking-wide truncate">Manajemen Dosen</span>
                    </a>
                </li>
            
                <!-- Manajemen Sidang (New) -->
                <li class="px-5">
                    <div class="flex flex-row items-center h-8">
                        <div class="text-sm font-light tracking-wide text-gray-500 uppercase">Manajemen Sidang</div>
                    </div>
                </li>
                
                <li x-data="{ open: {{ request()->routeIs('admin.defenses.internship.*') ? 'true' : 'false' }} }">
                    <button @click="open = !open" class="w-full relative flex flex-row items-center h-11 focus:outline-none hover:bg-indigo-700 hover:text-white text-gray-100 border-l-4 border-transparent {{ request()->routeIs('admin.defenses.internship.*') ? 'bg-indigo-700 border-indigo-400 text-white' : '' }} pr-6 justify-between">
                        <div class="flex items-center">
                            <span class="inline-flex justify-center items-center ml-4">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"></path></svg>
                            </span>
                            <span class="ml-2 text-sm tracking-wide truncate">Sidang Magang/KP</span>
                        </div>
                        <svg class="w-4 h-4 transition-transform duration-200" :class="{'transform rotate-180': open}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </button>
                    <ul x-show="open" class="bg-gray-900 py-2 space-y-1" style="display: none;">
                        <li>
                            <a href="{{ route('admin.defenses.internship.dashboard') }}" class="relative flex flex-row items-center h-10 focus:outline-none hover:bg-gray-700 text-gray-300 hover:text-white pl-12 pr-6 {{ request()->routeIs('admin.defenses.internship.dashboard') ? 'bg-gray-700 text-white' : '' }}">
                                <span class="text-sm tracking-wide truncate">Dashboard Ringkasan</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.defenses.internship.participants') }}" class="relative flex flex-row items-center h-10 focus:outline-none hover:bg-gray-700 text-gray-300 hover:text-white pl-12 pr-6 {{ request()->routeIs('admin.defenses.internship.participants') ? 'bg-gray-700 text-white' : '' }}">
                                <span class="text-sm tracking-wide truncate">Tarik Pendaftar Baru</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.defenses.internship.schedule') }}" class="relative flex flex-row items-center h-10 focus:outline-none hover:bg-gray-700 text-gray-300 hover:text-white pl-12 pr-6 {{ request()->routeIs('admin.defenses.internship.schedules') ? 'bg-gray-700 text-white' : '' }}">
                                <span class="text-sm tracking-wide truncate">Penjadwalan Sidang</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.defenses.internship.mentor-score') }}" class="relative flex flex-row items-center h-10 focus:outline-none hover:bg-gray-700 text-gray-300 hover:text-white pl-12 pr-6 {{ request()->routeIs('admin.defenses.internship.mentor-scores') ? 'bg-gray-700 text-white' : '' }}">
                                <span class="text-sm tracking-wide truncate">Nilai Mentor</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.defenses.internship.recap') }}" class="relative flex flex-row items-center h-10 focus:outline-none hover:bg-gray-700 text-gray-300 hover:text-white pl-12 pr-6 {{ request()->routeIs('admin.defenses.internship.recap') ? 'bg-gray-700 text-white' : '' }}">
                                <span class="text-sm tracking-wide truncate">Rekap & Dokumen</span>
                            </a>
                        </li>
                    </ul>
                </li>
</ul>
        </div>
    </div>

    <!-- Main Content -->
    <div class="flex-1 flex flex-col h-screen overflow-hidden">
        <!-- Header -->
        <header class="bg-white shadow-sm h-16 flex items-center justify-between px-6 z-10">
            <div class="flex items-center">
                <h1 class="text-xl font-semibold text-gray-800">@yield('title', 'Dashboard')</h1>
            </div>
            <div class="flex items-center">
                <div class="relative flex items-center gap-4">
                    <span class="text-sm text-gray-700">{{ Auth::guard('web')->check() ? Auth::guard('web')->user()->name : '' }}</span>
                    @if(Auth::guard('web')->check() && \App\Models\Lecturer::where('user_id', Auth::id())->exists())
                    <a href="{{ route('lecturer.profile') }}" class="text-sm text-gray-700 hover:text-indigo-600 font-medium">
                        Profil & TTD
                    </a>
                    @endif
                    <form method="POST" action="{{ route('admin.logout') }}">
                        @csrf
                        <button type="submit" class="text-sm text-white bg-red-600 hover:bg-red-700 px-3 py-1 rounded-md flex items-center">
                            Logout
                        </button>
                    </form>
                </div>
            </div>
        </header>

        <!-- Main -->
        <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-100 p-6">
            @yield('content')
            {{ $slot ?? '' }}
        </main>
    </div>

    @livewireScripts
    @stack('scripts')
</body>
</html>
