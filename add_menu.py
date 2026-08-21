import sys

file_path = r'c:\Users\userJ\Documents\fortain\resources\views\layouts\admin.blade.php'

with open(file_path, 'r', encoding='utf-8') as f:
    content = f.read()

# Add a submenu under MANAJEMEN SIDANG
new_menu = """        <div class="px-4 py-2 mt-4 text-xs font-semibold text-gray-400 uppercase tracking-wider">
            MANAJEMEN SIDANG
        </div>
        <a href="{{ route('admin.defenses.internship.dashboard') }}" class="block px-4 py-2 mt-2 text-sm font-semibold {{ request()->routeIs('admin.defenses.internship.dashboard') ? 'bg-indigo-600 text-white' : 'text-gray-300 hover:bg-gray-700' }}">
            <div class="flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                Admin - Dashboard Sidang
            </div>
        </a>
        <a href="{{ route('lecturer.defenses.internship.my-defenses') }}" class="block px-4 py-2 mt-2 text-sm font-semibold {{ request()->routeIs('lecturer.defenses.internship.*') ? 'bg-indigo-600 text-white' : 'text-gray-300 hover:bg-gray-700' }}">
            <div class="flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                Dosen - Sidang Saya
            </div>
        </a>
        <a href="{{ route('student.defenses.internship.status') }}" class="block px-4 py-2 mt-2 text-sm font-semibold {{ request()->routeIs('student.defenses.internship.*') ? 'bg-indigo-600 text-white' : 'text-gray-300 hover:bg-gray-700' }}">
            <div class="flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14zm-4 6v-7.5l4-2.222"></path></svg>
                Mahasiswa - Status Sidang
            </div>
        </a>"""

content = content.replace('''        <div class="px-4 py-2 mt-4 text-xs font-semibold text-gray-400 uppercase tracking-wider">
            MANAJEMEN SIDANG
        </div>
        <a href="{{ route('admin.defenses.internship.dashboard') }}" class="block px-4 py-2 mt-2 text-sm font-semibold {{ request()->routeIs('admin.defenses.internship.*') ? 'bg-indigo-600 text-white' : 'text-gray-300 hover:bg-gray-700' }}">
            <div class="flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                Sidang Magang/KP
            </div>
        </a>''', new_menu)

with open(file_path, 'w', encoding='utf-8') as f:
    f.write(content)
