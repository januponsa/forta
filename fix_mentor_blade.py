import sys
import re

file_path = r'c:\Users\userJ\Documents\fortain\resources\views\livewire\admin\defense\mentor-score-input.blade.php'

with open(file_path, 'r', encoding='utf-8') as f:
    content = f.read()

replacement = """                    <div class="relative flex-1 flex flex-col md:flex-row">
                        <!-- Kiri: Preview Dokumen -->
                        <div class="w-full md:w-3/5 border-b md:border-b-0 md:border-r border-gray-200 p-4 bg-gray-100 flex flex-col overflow-y-auto">
                            <div class="flex justify-between items-center mb-4">
                                <h3 class="text-sm font-medium text-gray-700">Dokumen Penilaian Mentor</h3>
                                <div class="flex space-x-2">
                                    <a id="pdf-new-tab-btn" href="#" target="_blank" rel="noopener" class="hidden inline-flex items-center px-2.5 py-1.5 border border-gray-300 shadow-sm text-xs font-medium rounded text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                        Buka di Tab Baru
                                    </a>
                                    <a id="pdf-download-btn" href="#" class="hidden inline-flex items-center px-2.5 py-1.5 border border-transparent text-xs font-medium rounded shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                        Download PDF
                                    </a>
                                </div>
                            </div>
                            
                            <div wire:ignore class="flex-1 w-full bg-white border border-gray-300 rounded overflow-hidden flex flex-col relative" id="pdf-viewer-container">
                                <!-- Toolbar -->
                                <div id="pdf-toolbar" class="hidden bg-gray-200 p-2 border-b border-gray-300 flex justify-between items-center text-sm">
                                    <div class="flex space-x-2">
                                        <button id="pdf-prev" class="px-2 py-1 bg-white border rounded shadow-sm hover:bg-gray-50">&larr; Seb</button>
                                        <span class="flex items-center">Hal <span id="pdf-page-num" class="mx-1 font-bold">1</span> dari <span id="pdf-page-count" class="mx-1">?</span></span>
                                        <button id="pdf-next" class="px-2 py-1 bg-white border rounded shadow-sm hover:bg-gray-50">Ber &rarr;</button>
                                    </div>
                                    <div class="flex space-x-2">
                                        <button id="pdf-zoom-out" class="px-2 py-1 bg-white border rounded shadow-sm hover:bg-gray-50">-</button>
                                        <button id="pdf-zoom-in" class="px-2 py-1 bg-white border rounded shadow-sm hover:bg-gray-50">+</button>
                                        <button id="pdf-fit" class="px-2 py-1 bg-white border rounded shadow-sm hover:bg-gray-50">Fit</button>
                                    </div>
                                </div>
                                
                                <!-- Canvas Container -->
                                <div id="pdf-canvas-container" class="flex-1 overflow-auto bg-gray-100 flex justify-center p-4 relative">
                                    <canvas id="pdf-render-canvas"></canvas>
                                </div>

                                <!-- Loading / Error Overlays -->
                                <div id="pdf-loading" class="absolute inset-0 bg-white bg-opacity-90 flex flex-col justify-center items-center">
                                    <svg class="animate-spin h-8 w-8 text-indigo-600 mb-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    <span class="text-sm text-gray-600">Memuat PDF...</span>
                                </div>
                                <div id="pdf-error" class="hidden absolute inset-0 bg-white flex flex-col justify-center items-center p-6 text-center">
                                    <svg class="h-12 w-12 text-red-500 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                                    <p id="pdf-error-text" class="text-sm text-gray-800 font-medium mb-4">Gagal memuat PDF.</p>
                                    <button id="pdf-retry-btn" class="px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">Coba Muat Ulang</button>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Kanan: Form Input -->
                        <div class="w-full md:w-2/5 p-6 overflow-y-auto">"""

pattern = re.compile(r'\s*<div class="relative flex-1 flex">.*?<!-- Kanan: Form Input -->\s*<div class="w-1/2 p-6 overflow-y-auto">', re.DOTALL)
content = pattern.sub('\n' + replacement, content)

# I should also add the script Vite tag if needed. I will rely on app.js importing mentor-pdf-viewer.js or adding it to vite.config.js directly.

with open(file_path, 'w', encoding='utf-8') as f:
    f.write(content)
