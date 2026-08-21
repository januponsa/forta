<div>
    @section('title', 'Manajemen Sidang - Rekap & Dokumen')
    
    <div class="px-6 py-5 bg-white border-b border-gray-200">
        <div class="flex justify-between items-center">
            <div>
                <h3 class="text-xl font-semibold text-gray-900">
                    Rekap Nilai & Dokumen Sidang KP
                </h3>
                <p class="text-sm text-gray-500 mt-1">Kelola rekap nilai dan generate dokumen F1-F6 sidang Kerja Praktik</p>
            </div>
            <div>
                <select wire:model.live="semesterFilter" class="rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 text-sm">
                    <option value="">Semua Semester</option>
                    @foreach($semesters as $sem)
                        <option value="{{ $sem }}">{{ $sem }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>
    
    <div class="p-6">
        {{-- Flash Messages --}}
        @if (session()->has('message'))
            <div class="mb-5 bg-green-50 border border-green-200 rounded-lg p-4 flex items-start">
                <svg class="w-5 h-5 text-green-500 mr-3 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                <p class="text-sm text-green-800">{{ session('message') }}</p>
            </div>
        @endif
        
        @if (session()->has('error'))
            <div class="mb-5 bg-red-50 border border-red-200 rounded-lg p-4 flex items-start">
                <svg class="w-5 h-5 text-red-500 mr-3 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path></svg>
                <p class="text-sm text-red-800">{{ session('error') }}</p>
            </div>
        @endif

        {{-- Cases Table --}}
        <div class="bg-white shadow-sm rounded-lg border border-gray-200 overflow-visible">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Mahasiswa</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jadwal Sidang</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Nilai</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Dokumen</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($cases as $idx => $case)
                        <tr class="hover:bg-gray-50 transition-colors duration-150">
                            {{-- No --}}
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $idx + 1 }}
                            </td>
                            
                            {{-- Mahasiswa --}}
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-semibold text-gray-900">{{ $case->student->name ?? 'Unknown' }}</div>
                                <div class="text-xs text-gray-500">NIM: {{ $case->student->nim ?? '-' }}</div>
                            </td>
                            
                            {{-- Jadwal Sidang --}}
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($case->latestSchedule)
                                    <div class="text-sm text-gray-900">
                                        {{ \Carbon\Carbon::parse($case->latestSchedule->scheduled_at)->isoFormat('D MMM YYYY') }}
                                    </div>
                                    <div class="text-xs text-gray-500">
                                        {{ \Carbon\Carbon::parse($case->latestSchedule->scheduled_at)->format('H:i') }} WIB
                                    </div>
                                @else
                                    <span class="text-xs text-gray-400 italic">Belum dijadwalkan</span>
                                @endif
                            </td>
                            
                            {{-- Nilai --}}
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                @if($case->final_score)
                                    <div class="text-lg font-bold text-gray-900">{{ number_format($case->final_score, 1) }}</div>
                                    <div class="text-xs font-semibold px-2 py-0.5 rounded-full inline-block
                                        @if(in_array($case->final_grade, ['A', 'A-'])) bg-green-100 text-green-800
                                        @elseif(in_array($case->final_grade, ['B+', 'B', 'B-'])) bg-blue-100 text-blue-800
                                        @elseif(in_array($case->final_grade, ['C+', 'C'])) bg-yellow-100 text-yellow-800
                                        @else bg-red-100 text-red-800
                                        @endif
                                    ">{{ $case->final_grade }}</div>
                                @else
                                    <span class="text-sm text-gray-400">-</span>
                                @endif
                            </td>
                            
                            {{-- Status --}}
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                @if(in_array($case->status, ['passed', 'passed_with_revision', 'completed']))
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800">
                                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>
                                        LULUS
                                    </span>
                                @elseif($case->status === 'failed')
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-800">
                                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>
                                        TIDAK LULUS
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-800">
                                        {{ str_replace('_', ' ', Str::title($case->status)) }}
                                    </span>
                                @endif
                            </td>
                            
                            {{-- Dokumen --}}
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                @if($case->documents->count() > 0)
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800">
                                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd"></path></svg>
                                        {{ $case->documents->count() }} File
                                    </span>
                                @else
                                    <span class="text-xs text-gray-400 italic">Belum ada</span>
                                @endif
                            </td>
                            
                            {{-- Aksi --}}
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <div class="flex flex-col items-center space-y-2">
                                    {{-- Generate PDF Button --}}
                                    <button
                                        wire:click="openDocumentModal({{ $case->id }})"
                                        wire:loading.attr="disabled"
                                        wire:target="openDocumentModal({{ $case->id }})"
                                        class="inline-flex items-center px-3 py-1.5 bg-indigo-600 text-white text-xs font-semibold rounded-lg hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-1 transition-colors duration-150 disabled:opacity-50 disabled:cursor-not-allowed shadow-sm"
                                    >
                                        <span wire:loading.remove wire:target="openDocumentModal({{ $case->id }})">
                                            <svg class="w-3.5 h-3.5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                        </span>
                                        <span wire:loading wire:target="openDocumentModal({{ $case->id }})">
                                            <svg class="animate-spin w-3.5 h-3.5 mr-1.5" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                        </span>
                                        <span wire:loading.remove wire:target="openDocumentModal({{ $case->id }})">Generate PDF</span>
                                        <span wire:loading wire:target="openDocumentModal({{ $case->id }})">Loading...</span>
                                    </button>
                                    
                                    {{-- Input Nilai Links --}}
                                    <div class="flex flex-wrap gap-1 justify-center">
                                        <a href="{{ route('admin.defenses.internship.score', ['caseId' => $case->id, 'role' => 'supervisor']) }}" 
                                           class="inline-flex items-center px-2 py-1 text-xs text-blue-700 bg-blue-50 hover:bg-blue-100 rounded-md border border-blue-200 transition-colors duration-150">
                                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                            Nilai Pembimbing
                                        </a>
                                        <a href="{{ route('admin.defenses.internship.score', ['caseId' => $case->id, 'role' => 'examiner']) }}" 
                                           class="inline-flex items-center px-2 py-1 text-xs text-purple-700 bg-purple-50 hover:bg-purple-100 rounded-md border border-purple-200 transition-colors duration-150">
                                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                            Nilai Penguji
                                        </a>
                                    </div>
                                    
                                    {{-- Download Documents --}}
                                    @if($case->documents->count() > 0)
                                        <div class="w-full border-t border-gray-100 pt-2 mt-1">
                                            <div class="text-xs font-medium text-gray-500 mb-1.5">Unduh Dokumen:</div>
                                            <div class="flex flex-col gap-1">
                                                @foreach($case->documents as $doc)
                                                    <a href="{{ route('admin.defenses.internship.documents.download', $doc->id) }}" 
                                                       class="inline-flex items-center text-xs text-indigo-700 hover:text-indigo-900 bg-indigo-50 hover:bg-indigo-100 px-2 py-1 rounded-md transition-colors duration-150 border border-indigo-100">
                                                        <svg class="w-3 h-3 mr-1.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                                                        <span class="truncate">{{ $doc->original_name ?? 'Dokumen' }}</span>
                                                    </a>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center">
                                <svg class="mx-auto h-12 w-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                <h3 class="mt-2 text-sm font-medium text-gray-900">Belum ada data sidang</h3>
                                <p class="mt-1 text-sm text-gray-500">Data sidang KP akan muncul setelah mahasiswa didaftarkan.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @if($showEditModal)
    <div class="fixed inset-0 z-[100] flex items-center justify-center p-4 sm:p-6" aria-labelledby="modal-title" role="dialog" aria-modal="true"
         x-data="{ 
            activeTab: @entangle('activeTab'),
            formatText(command, value = null) {
                const iframe = document.getElementById('iframe-' + this.activeTab);
                if (iframe && iframe.contentWindow) {
                    iframe.contentWindow.postMessage({
                        action: 'format',
                        command: command,
                        value: value
                    }, '*');
                }
            }
         }"
         x-on:message.window="if($event.data && $event.data.type === 'update_html') { $wire.updateDocumentHtml($event.data.docType, $event.data.html) }">
         
        <!-- Backdrop -->
        <div class="fixed inset-0 bg-gray-600 bg-opacity-75 transition-opacity" wire:click="closeEditModal"></div>

        <!-- Modal Panel -->
        <div class="relative bg-white rounded-xl shadow-2xl flex flex-col w-full max-w-7xl h-[90vh] overflow-hidden z-10">
            <!-- Header -->
            <div class="bg-white px-6 py-4 border-b border-gray-200 flex flex-col sm:flex-row justify-between items-start sm:items-center shrink-0 gap-4">
                <h3 class="text-xl font-semibold text-gray-900" id="modal-title">
                    Preview & Edit Dokumen Sidang
                </h3>
                
                <!-- WYSIWYG Toolbar -->
                <div class="flex items-center space-x-2 bg-gray-100 p-1.5 rounded-md border border-gray-300 shadow-sm" x-show="activeTab !== ''">
                    <!-- Font Family -->
                    <select @change="formatText('fontName', $event.target.value)" class="text-sm border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 py-1 pl-2 pr-6">
                        <option value="">-- Pilih Font --</option>
                        <option value="'Times New Roman', Times, serif">Times New Roman</option>
                        <option value="Arial, sans-serif">Arial</option>
                        <option value="Helvetica, sans-serif">Helvetica</option>
                        <option value="Calibri, sans-serif">Calibri</option>
                        <option value="'Titillium Web', sans-serif">Titillium Web</option>
                    </select>

                    <div class="w-px h-6 bg-gray-300 mx-1"></div>

                    <!-- Text Styles -->
                    <button @click="formatText('bold')" type="button" class="p-1.5 text-gray-700 hover:bg-gray-200 rounded" title="Bold (Ctrl+B)">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M15.6 11.8c1-.7 1.6-1.8 1.6-3 0-2.3-1.9-4.1-4.2-4.1H7v14h6.4c2.5 0 4.5-1.9 4.5-4.2 0-1.4-.7-2.6-1.9-3.2v-.2zM10 7.5h2.6c.9 0 1.6.7 1.6 1.6s-.7 1.6-1.6 1.6H10V7.5zm3.1 8.4H10v-3.4h3.1c1.1 0 2 .9 2 2s-.9 1.4-2 1.4z"/></svg>
                    </button>
                    <button @click="formatText('italic')" type="button" class="p-1.5 text-gray-700 hover:bg-gray-200 rounded" title="Italic (Ctrl+I)">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M10 5.5v3h2.2l-3.4 8H6.5v3h8v-3h-2.2l3.4-8H18v-3h-8z"/></svg>
                    </button>
                    <button @click="formatText('underline')" type="button" class="p-1.5 text-gray-700 hover:bg-gray-200 rounded" title="Underline (Ctrl+U)">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M12 17c3.3 0 6-2.7 6-6V3h-2.5v8c0 1.9-1.6 3.5-3.5 3.5S8.5 12.9 8.5 11V3H6v8c0 3.3 2.7 6 6 6zm-7 2v1.5h14V19H5z"/></svg>
                    </button>
                    <button @click="formatText('strikeThrough')" type="button" class="p-1.5 text-gray-700 hover:bg-gray-200 rounded" title="Strikethrough">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M10 19h4v-3h-4v3zM5 4v3h5v3h4V7h5V4H5zM3 14h18v-2H3v2z"/></svg>
                    </button>

                    <div class="w-px h-6 bg-gray-300 mx-1"></div>

                    <!-- Alignment -->
                    <button @click="formatText('justifyLeft')" type="button" class="p-1.5 text-gray-700 hover:bg-gray-200 rounded" title="Align Left">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M15 15H3v2h12v-2zm0-8H3v2h12V7zM3 13h18v-2H3v2zm0 8h18v-2H3v2zM3 3v2h18V3H3z"/></svg>
                    </button>
                    <button @click="formatText('justifyCenter')" type="button" class="p-1.5 text-gray-700 hover:bg-gray-200 rounded" title="Align Center">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M7 15v2h10v-2H7zm-4 6h18v-2H3v2zm0-8h18v-2H3v2zm4-6v2h10V7H7zM3 3v2h18V3H3z"/></svg>
                    </button>
                    <button @click="formatText('justifyRight')" type="button" class="p-1.5 text-gray-700 hover:bg-gray-200 rounded" title="Align Right">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M3 21h18v-2H3v2zm6-4h12v-2H9v2zm-6-4h18v-2H3v2zm6-4h12V7H9v2zM3 3v2h18V3H3z"/></svg>
                    </button>
                    <button @click="formatText('justifyFull')" type="button" class="p-1.5 text-gray-700 hover:bg-gray-200 rounded" title="Justify">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M3 21h18v-2H3v2zm0-4h18v-2H3v2zm0-4h18v-2H3v2zm0-4h18V7H3v2zm0-6v2h18V3H3z"/></svg>
                    </button>
                </div>

                <button type="button" wire:click="closeEditModal" class="text-gray-400 hover:text-gray-500 absolute top-4 right-4 sm:static">
                    <span class="sr-only">Tutup</span>
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <!-- Body -->
            <div class="flex-1 flex overflow-hidden bg-gray-50">
                
                <!-- Sidebar -->
                <div class="w-72 flex-shrink-0 bg-white border-r border-gray-200 flex flex-col overflow-y-auto p-4">
                    <h4 class="text-sm font-bold text-gray-900 uppercase tracking-wider mb-4">Daftar Dokumen</h4>
                    <nav class="flex flex-col space-y-1">
                        @foreach($availableDocuments as $type => $doc)
                        <button type="button" 
                            x-on:click="activeTab = '{{ $type }}'"
                            :class="activeTab === '{{ $type }}' ? 'bg-indigo-50 text-indigo-700 border-indigo-500 font-semibold' : 'text-gray-600 hover:bg-gray-50 border-transparent'"
                            class="text-left px-3 py-2 text-sm border-l-4 transition-colors flex items-center justify-between">
                            <span class="truncate">{{ $doc['title'] }}</span>
                            <!-- Status indicator if needed -->
                        </button>
                        @endforeach
                    </nav>
                    <div class="mt-6 p-4 bg-blue-50 rounded-md border border-blue-100">
                        <p class="text-xs text-blue-800 leading-relaxed">
                            <strong>Petunjuk:</strong><br>
                            Klik area teks di dalam dokumen untuk langsung mengedit isinya seperti di Word.
                        </p>
                    </div>
                </div>
                
                <!-- Iframe Container -->
                <div class="flex-1 p-4 bg-gray-200 relative overflow-hidden" wire:ignore>
                    <div class="w-full h-full bg-white rounded shadow-inner border border-gray-300 relative overflow-hidden">
                        @foreach($availableDocuments as $type => $doc)
                        <div x-show="activeTab === '{{ $type }}'" style="display: none;" class="absolute inset-0 w-full h-full">
                            <iframe id="iframe-{{ $type }}" src="{{ route('admin.defenses.document.preview', ['case' => $selectedCaseId, 'type' => $type]) }}" class="w-full h-full border-0 bg-white"></iframe>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div class="bg-gray-50 px-6 py-4 border-t border-gray-200 flex justify-end gap-3 shrink-0">
                <button type="button" wire:click="closeEditModal" class="px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Batal
                </button>
                <button type="button" wire:click="generateAllDocuments" wire:loading.attr="disabled" class="px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    <span wire:loading.remove wire:target="generateAllDocuments">Simpan & Generate PDF</span>
                    <span wire:loading wire:target="generateAllDocuments">Memproses...</span>
                </button>
            </div>
        </div>
    </div>
    @endif
</div>
