<div>
    <div class="mb-6 flex justify-between items-center">
        <h2 class="text-2xl font-bold text-gray-800">Email Blast Manager</h2>
        <div>
            <a href="{{ route('admin.email-blast.history') }}" class="text-blue-600 hover:underline mr-4">Riwayat Email</a>
        </div>
    </div>

    @if (session()->has('message'))
        <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative">
            {{ session('message') }}
        </div>
    @endif

    <!-- Tabs -->
    <div class="mb-4 border-b border-gray-200">
        <ul class="flex flex-wrap -mb-px text-sm font-medium text-center">
            <li class="mr-2">
                <button wire:click="$set('activeTab', 'audience')" class="inline-block p-4 border-b-2 rounded-t-lg {{ $activeTab === 'audience' ? 'text-blue-600 border-blue-600' : 'hover:text-gray-600 hover:border-gray-300' }}">1. Pilih Penerima</button>
            </li>
            <li class="mr-2">
                <button wire:click="$set('activeTab', 'compose')" class="inline-block p-4 border-b-2 rounded-t-lg {{ $activeTab === 'compose' ? 'text-blue-600 border-blue-600' : 'hover:text-gray-600 hover:border-gray-300' }}">2. Tulis Email</button>
            </li>
            <li class="mr-2">
                <button wire:click="$set('activeTab', 'preview')" class="inline-block p-4 border-b-2 rounded-t-lg {{ $activeTab === 'preview' ? 'text-blue-600 border-blue-600' : 'hover:text-gray-600 hover:border-gray-300' }}">3. Preview & Kirim</button>
            </li>
        </ul>
    </div>

    <div>
        @if ($activeTab === 'audience')
            <div class="bg-white p-6 rounded shadow-sm">
                <h3 class="text-lg font-semibold mb-4">Pilih Target Penerima</h3>
                
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Metode Pemilihan</label>
                    <select wire:model.live="audienceType" class="shadow border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                        <option value="all_active">Semua Mahasiswa Aktif</option>
                        <option value="by_batch">Pilih Angkatan</option>
                        <option value="manual">Pilih Mahasiswa (Manual)</option>
                    </select>
                </div>

                @if ($audienceType === 'by_batch')
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2">Angkatan</label>
                        <div class="grid grid-cols-4 gap-2">
                            @foreach($availableBatches as $batch)
                                <label class="inline-flex items-center">
                                    <input type="checkbox" wire:model="selectedBatches" value="{{ $batch }}" class="form-checkbox h-5 w-5 text-blue-600">
                                    <span class="ml-2 text-gray-700">{{ $batch }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                @endif

                @if ($audienceType === 'manual')
                    <div class="mb-4 bg-gray-50 p-4 border rounded">
                        <div class="flex space-x-4 mb-4">
                            <input type="text" wire:model.live.debounce.300ms="searchStudent" placeholder="Cari Nama/NIM..." class="shadow border rounded w-full py-2 px-3">
                            <select wire:model.live="filterBatch" class="shadow border rounded py-2 px-3">
                                <option value="">Semua Angkatan</option>
                                @foreach($availableBatches as $batch)
                                    <option value="{{ $batch }}">{{ $batch }}</option>
                                @endforeach
                            </select>
                            <button wire:click="selectAllFiltered" class="bg-blue-500 text-white px-4 py-2 rounded whitespace-nowrap">Pilih Hasil</button>
                            <button wire:click="clearSelection" class="bg-red-500 text-white px-4 py-2 rounded whitespace-nowrap">Hapus Pilihan ({{ count($selectedStudents) }})</button>
                        </div>
                        
                        <div class="overflow-x-auto">
                            <table class="min-w-full bg-white">
                                <thead class="bg-gray-100">
                                    <tr>
                                        <th class="py-2 px-4 border-b">Pilih</th>
                                        <th class="py-2 px-4 border-b">NIM</th>
                                        <th class="py-2 px-4 border-b">Nama</th>
                                        <th class="py-2 px-4 border-b">Angkatan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($students as $student)
                                        <tr>
                                            <td class="py-2 px-4 border-b text-center">
                                                <input type="checkbox" wire:model="selectedStudents" value="{{ $student->id }}">
                                            </td>
                                            <td class="py-2 px-4 border-b">{{ $student->nim }}</td>
                                            <td class="py-2 px-4 border-b">{{ $student->name }}</td>
                                            <td class="py-2 px-4 border-b">{{ $student->angkatan }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            <div class="mt-4">
                                {{ $students->links() }}
                            </div>
                        </div>
                    </div>
                @endif
                
                <div class="flex justify-end mt-6">
                    <button wire:click="$set('activeTab', 'compose')" class="bg-blue-600 text-white font-bold py-2 px-4 rounded">Selanjutnya</button>
                </div>
            </div>
        @endif

        @if ($activeTab === 'compose')
            <div class="bg-white p-6 rounded shadow-sm">
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Subject</label>
                    <input type="text" wire:model="subject" class="shadow border rounded w-full py-2 px-3 text-gray-700">
                    @error('subject') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
                
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Mode Pengiriman</label>
                    <select wire:model="deliveryMode" class="shadow border rounded w-full py-2 px-3 text-gray-700">
                        <option value="to">Kirim Bersama via To</option>
                        <option value="cc">Kirim Bersama via CC</option>
                        <option value="bcc">Kirim Bersama via BCC</option>
                        <option value="individual">Kirim Individual (Bisa pakai Placeholder)</option>
                    </select>
                    <p class="text-xs text-gray-500 mt-1">Gunakan 'Kirim Individual' jika ingin memakai <code>@{{nama}}</code>, <code>@{{nim}}</code>, dll.</p>
                </div>

                <div class="mb-4" wire:ignore>
                    <label class="block text-gray-700 text-sm font-bold mb-2">Body (HTML)</label>
                    <textarea wire:model="bodyHtml" rows="10" class="shadow border rounded w-full py-2 px-3 text-gray-700 font-mono"></textarea>
                    <p class="text-xs text-gray-500 mt-1">Placeholder: <code>@{{nama}}</code>, <code>@{{nim}}</code>, <code>@{{angkatan}}</code>, <code>@{{email}}</code></p>
                    @error('bodyHtml') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Lampiran (Bisa lebih dari 1)</label>
                    <input type="file" wire:model="attachments" multiple class="shadow border rounded w-full py-2 px-3 text-gray-700">
                    <div wire:loading wire:target="attachments" class="text-sm text-blue-500 mt-1">Uploading...</div>
                    @if ($attachments)
                        <ul class="list-disc pl-5 mt-2 text-sm text-gray-600">
                            @foreach($attachments as $file)
                                <li>{{ $file->getClientOriginalName() }} ({{ round($file->getSize() / 1024) }} KB)</li>
                            @endforeach
                        </ul>
                    @endif
                </div>

                <div class="flex justify-between mt-6">
                    <button wire:click="$set('activeTab', 'audience')" class="bg-gray-500 text-white font-bold py-2 px-4 rounded">Kembali</button>
                    <button wire:click="$set('activeTab', 'preview')" class="bg-blue-600 text-white font-bold py-2 px-4 rounded">Preview & Kirim</button>
                </div>
            </div>
        @endif

        @if ($activeTab === 'preview')
            <div class="bg-white p-6 rounded shadow-sm">
                <h3 class="text-lg font-semibold mb-4 border-b pb-2">Ringkasan Pengiriman</h3>
                
                <div class="grid grid-cols-2 gap-4 mb-6">
                    <div>
                        <p class="text-sm text-gray-500">Subject</p>
                        <p class="font-bold">{{ $subject ?: '-' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Mode Kirim</p>
                        <p class="font-bold uppercase">{{ $deliveryMode }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Kategori Target</p>
                        <p class="font-bold">
                            @if($audienceType === 'all_active') Semua Mahasiswa Aktif
                            @elseif($audienceType === 'by_batch') Angkatan: {{ implode(', ', $selectedBatches) }}
                            @else Pilihan Manual ({{ count($selectedStudents) }})
                            @endif
                        </p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Jadwal Kirim</p>
                        <select wire:model="scheduleType" class="shadow border rounded py-1 px-2 text-sm">
                            <option value="now">Kirim Sekarang</option>
                            <option value="scheduled">Jadwalkan</option>
                        </select>
                        @if ($scheduleType === 'scheduled')
                            <input type="datetime-local" wire:model="scheduledAt" class="shadow border rounded py-1 px-2 text-sm mt-2 block">
                        @endif
                    </div>
                </div>

                <div class="border rounded p-4 mb-6 bg-gray-50 prose max-w-none">
                    {!! nl2br(e($bodyHtml)) !!}
                </div>

                <div class="flex space-x-4 mb-6 border-t pt-4">
                    <button wire:click="sendTestEmail" class="bg-yellow-500 hover:bg-yellow-600 text-white font-bold py-2 px-4 rounded">Kirim Email Percobaan (ke Admin)</button>
                </div>
                
                @error('audienceType') <div class="text-red-500 mb-4">{{ $message }}</div> @enderror

                <div class="flex justify-between mt-6 border-t pt-4">
                    <button wire:click="$set('activeTab', 'compose')" class="bg-gray-500 text-white font-bold py-2 px-4 rounded">Kembali Edit</button>
                    <button wire:click="submitCampaign" wire:loading.attr="disabled" class="bg-green-600 hover:bg-green-700 text-white font-bold py-3 px-6 rounded shadow-lg flex items-center">
                        <span wire:loading.remove wire:target="submitCampaign">Kirim Kampanye Email</span>
                        <span wire:loading wire:target="submitCampaign">Memproses...</span>
                    </button>
                </div>
            </div>
        @endif
    </div>
</div>
