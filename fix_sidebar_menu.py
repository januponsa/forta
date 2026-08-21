import sys
import re

file_path = r'c:\Users\userJ\Documents\fortain\resources\views\layouts\admin.blade.php'

with open(file_path, 'r', encoding='utf-8') as f:
    content = f.read()

old_menu = """                <li>
                    <a href="{{ route('admin.defenses.internship.dashboard') }}" class="relative flex flex-row items-center h-11 focus:outline-none hover:bg-indigo-700 hover:text-white text-gray-100 border-l-4 border-transparent {{ request()->routeIs('admin.defenses.internship.*') ? 'bg-indigo-700 border-indigo-400 text-white' : '' }}">
                        <span class="inline-flex justify-center items-center ml-4">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"></path></svg>
                        </span>
                        <span class="ml-2 text-sm tracking-wide truncate">Sidang Magang/KP</span>
                    </a>
                </li>"""

new_menu = """                <li x-data="{ open: {{ request()->routeIs('admin.defenses.internship.*') ? 'true' : 'false' }} }">
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
                            <a href="{{ route('admin.defenses.internship.schedules') }}" class="relative flex flex-row items-center h-10 focus:outline-none hover:bg-gray-700 text-gray-300 hover:text-white pl-12 pr-6 {{ request()->routeIs('admin.defenses.internship.schedules') ? 'bg-gray-700 text-white' : '' }}">
                                <span class="text-sm tracking-wide truncate">Penjadwalan Sidang</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.defenses.internship.mentor-scores') }}" class="relative flex flex-row items-center h-10 focus:outline-none hover:bg-gray-700 text-gray-300 hover:text-white pl-12 pr-6 {{ request()->routeIs('admin.defenses.internship.mentor-scores') ? 'bg-gray-700 text-white' : '' }}">
                                <span class="text-sm tracking-wide truncate">Nilai Mentor</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.defenses.internship.recap') }}" class="relative flex flex-row items-center h-10 focus:outline-none hover:bg-gray-700 text-gray-300 hover:text-white pl-12 pr-6 {{ request()->routeIs('admin.defenses.internship.recap') ? 'bg-gray-700 text-white' : '' }}">
                                <span class="text-sm tracking-wide truncate">Rekap & Dokumen</span>
                            </a>
                        </li>
                    </ul>
                </li>"""

if old_menu in content:
    content = content.replace(old_menu, new_menu)
else:
    print("Old menu block not found in admin.blade.php. Attempting regex fallback.")
    import re
    # Just replace from <li> ... route('admin.defenses.internship.dashboard') ... </li>
    pattern = re.compile(r'<li>\s*<a href="\{\{ route\(\'admin\.defenses\.internship\.dashboard\'\).*?</li>', re.DOTALL)
    content = pattern.sub(new_menu, content)


with open(file_path, 'w', encoding='utf-8') as f:
    f.write(content)
