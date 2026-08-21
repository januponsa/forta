<div>
    @section('title', 'Hasil Akhir Sidang')
    
    <div class="px-4 py-5 sm:px-6 bg-white border-b border-gray-200 flex justify-between items-center">
        <h3 class="text-lg leading-6 font-medium text-gray-900">
            Hasil Akhir Sidang Magang/KP
        </h3>
        <div>
            <a href="{{ route('student.defenses.internship.status') }}" class="text-sm text-indigo-600 hover:text-indigo-900">
                &larr; Kembali ke Status
            </a>
        </div>
    </div>
    
    <div class="p-6">
        @if(!$isFinalized)
            <div class="text-center py-10 bg-white shadow rounded-lg border-2 border-dashed border-gray-300">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">Penilaian Sedang Diproses</h3>
                <p class="mt-1 text-sm text-gray-500">Nilai akhir Anda belum diterbitkan atau dosen belum menyelesaikan proses penilaian. Silakan cek secara berkala.</p>
            </div>
        @else
            <div class="bg-white shadow overflow-hidden sm:rounded-lg">
                <div class="px-4 py-5 sm:px-6 flex justify-between items-center bg-gray-50 border-b">
                    <div>
                        <h3 class="text-lg leading-6 font-medium text-gray-900">
                            Ringkasan Penilaian Sidang
                        </h3>
                    </div>
                    <div>
                        @if($defenseCase->final_grade)
                            <span class="px-4 py-2 inline-flex text-xl leading-5 font-bold rounded-md bg-indigo-100 text-indigo-800">
                                Grade: {{ $defenseCase->final_grade }}
                            </span>
                        @endif
                    </div>
                </div>
                
                <div class="border-t border-gray-200 p-6">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                        @foreach($defenseCase->assessments as $assessment)
                            <div class="bg-white border rounded-lg p-4 text-center shadow-sm">
                                <h4 class="text-sm font-medium text-gray-500 uppercase tracking-wide">
                                    {{ $assessment->assessor_role === 'supervisor' ? 'Dosen Pembimbing' : ($assessment->assessor_role === 'examiner' ? 'Dosen Penguji' : 'Mentor Lapangan') }}
                                </h4>
                                <div class="mt-2 flex items-baseline justify-center text-4xl font-extrabold text-indigo-600">
                                    {{ $assessment->total_score }}
                                    <span class="ml-1 text-xl font-medium text-gray-500">/100</span>
                                </div>
                                <p class="mt-1 text-xs text-gray-400">
                                    Bobot: {{ $assessment->assessor_role === 'mentor' ? '40%' : '30%' }}
                                </p>
                            </div>
                        @endforeach
                    </div>
                    
                    <div class="bg-gray-50 rounded-lg p-6 border text-center">
                        <h4 class="text-lg font-medium text-gray-700">Skor Akhir Gabungan</h4>
                        <div class="mt-2 text-5xl font-extrabold text-gray-900">
                            {{ $defenseCase->final_score ?? '-' }}
                        </div>
                        <div class="mt-4">
                            @if(in_array($defenseCase->status, ['passed', 'passed_with_revision']))
                                <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full bg-green-100 text-green-800">LULUS</span>
                            @elseif($defenseCase->status === 'failed')
                                <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full bg-red-100 text-red-800">TIDAK LULUS</span>
                            @else
                                <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">{{ str_replace('_', ' ', Str::title($defenseCase->status)) }}</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="mt-8 bg-white shadow sm:rounded-lg">
                <div class="px-4 py-5 sm:px-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">Unduh Dokumen Berita Acara & Pengesahan</h3>
                    <p class="mt-1 max-w-2xl text-sm text-gray-500">Dokumen PDF resmi hasil persidangan Anda.</p>
                </div>
                <div class="border-t border-gray-200">
                    <ul role="list" class="divide-y divide-gray-200">
                        @forelse($defenseCase->documents as $doc)
                            <li class="pl-3 pr-4 py-3 flex items-center justify-between text-sm">
                                <div class="w-0 flex-1 flex items-center">
                                    <svg class="flex-shrink-0 h-5 w-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd" />
                                    </svg>
                                    <span class="ml-2 flex-1 w-0 truncate">
                                        {{ str_replace('_', ' ', Str::title($doc->document_type)) }}.pdf
                                    </span>
                                </div>
                                <div class="ml-4 flex-shrink-0">
                                    <a href="/storage/{{ $doc->file_path }}" target="_blank" class="font-medium text-indigo-600 hover:text-indigo-500">
                                        Download
                                    </a>
                                </div>
                            </li>
                        @empty
                            <li class="px-4 py-6 text-center text-sm text-gray-500">
                                Dokumen PDF belum diterbitkan oleh Admin.
                            </li>
                        @endforelse
                    </ul>
                </div>
            </div>
        @endif
    </div>
</div>
