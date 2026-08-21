<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Student Dashboard') - FORTA</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="bg-gray-50 font-sans leading-normal tracking-normal flex flex-col min-h-screen">
    <!-- Header -->
    <header class="bg-blue-600 shadow-sm h-16 flex items-center justify-between px-6 z-10 text-white">
        <div class="flex items-center space-x-6">
            <h1 class="text-xl font-bold"><a href="{{ route('student.dashboard') }}">FORTA Student</a></h1>
            <nav class="hidden md:flex space-x-4">
                <a href="{{ route('student.dashboard') }}" class="text-sm font-medium hover:text-gray-200">Dashboard</a>
                <div x-data="{ open: false }" class="relative" @mouseleave="open = false" @mouseenter="open = true">
                    <button @click="open = !open" class="flex items-center text-sm font-medium hover:text-gray-200 focus:outline-none">
                        Pengajuan Surat
                        <svg class="ml-1 w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>
                    </button>
                    <div x-show="open" x-transition class="absolute z-10 mt-2 w-56 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5" style="display: none;">
                        <div class="py-1">
                            <a href="{{ route('student.internship-letters.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Surat Magang/KP</a>
                            <a href="{{ route('student.signature-requests.index') ?? '#' }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Pengajuan Surat untuk Ditandatangani</a>
                        </div>
                    </div>
                </div>
            </nav>
        </div>
        <div class="flex items-center">
            <div class="relative flex items-center gap-4">
                @php
                    $student = \App\Models\Student::find(session('student_id'));
                @endphp
                <span class="text-sm">{{ $student->name ?? 'Student' }}</span>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="text-sm bg-blue-700 hover:bg-blue-800 px-3 py-1 rounded-md flex items-center">
                        Logout
                    </button>
                </form>
            </div>
        </div>
    </header>

    <!-- Main -->
    <main class="flex-grow w-full max-w-7xl mx-auto p-6">
        @yield('content')
        {{ $slot ?? '' }}
    </main>

    @livewireScripts
</body>
</html>
