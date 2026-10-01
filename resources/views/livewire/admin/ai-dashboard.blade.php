<div>
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Dashboard AI Analytics & Predictive</h2>
            <p class="text-sm text-gray-600 mt-1">Analisis pengajuan form dan deteksi mahasiswa berisiko terlambat.</p>
        </div>
    </div>

    @if (session()->has('message'))
        <div class="rounded-md bg-green-50 p-4 mb-6 border-l-4 border-green-500">
            <p class="text-sm font-medium text-green-800">{{ session('message') }}</p>
        </div>
    @endif
    
    @if (session()->has('error'))
        <div class="rounded-md bg-red-50 p-4 mb-6 border-l-4 border-red-500">
            <p class="text-sm font-medium text-red-800">{{ session('error') }}</p>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <!-- Visual Analytics Chart (Google Forms-like) -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Statistik Pengajuan Form</h3>
            <canvas id="formAnalyticsChart" height="200"></canvas>
        </div>
        
        <!-- Placeholder for more charts -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 flex items-center justify-center">
            <div class="text-center">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">AI Predictive Insights</h3>
                <p class="mt-1 text-sm text-gray-500">Sistem menganalisa {{ count($atRiskStudents) }} mahasiswa berisiko tinggi (At-Risk) dari riwayat aktivitas mereka.</p>
            </div>
        </div>
    </div>

    @if($aiResponse)
    <div class="bg-blue-50 rounded-lg shadow-sm border border-blue-200 p-6 mb-8">
        <h3 class="text-lg font-medium text-blue-900 mb-4">Preview AI Email yang Dikirim</h3>
        <div class="bg-white p-4 rounded border border-blue-100 whitespace-pre-wrap text-sm text-gray-800 font-mono">{{ $aiResponse }}</div>
        <button wire:click="$set('aiResponse', null)" class="mt-4 text-sm text-blue-600 hover:text-blue-800">Tutup Preview</button>
    </div>
    @endif

    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden mb-6">
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50 flex justify-between items-center">
            <h3 class="text-lg font-medium text-gray-900">Mahasiswa Berisiko (At-Risk Students)</h3>
        </div>
        
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Mahasiswa</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Risk Score</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Alasan (Analisis)</th>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi (AI Reminder)</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse ($atRiskStudents as $item)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $item['student']->name }}</div>
                                <div class="text-sm text-gray-500">{{ $item['student']->nim }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $item['level'] === 'Tinggi' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800' }}">
                                    Score: {{ $item['score'] }} ({{ $item['level'] }})
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <ul class="list-disc list-inside text-xs text-gray-600">
                                    @foreach($item['reasons'] as $reason)
                                        <li>{{ $reason }}</li>
                                    @endforeach
                                </ul>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <button wire:click="sendAiReminder({{ $item['student']->id }}, {{ json_encode($item['reasons']) }})" wire:loading.attr="disabled" class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50">
                                    <svg wire:loading wire:target="sendAiReminder({{ $item['student']->id }})" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    Kirim AI Reminder
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-8 text-center text-gray-500">
                                Tidak ditemukan mahasiswa yang berisiko tertinggal.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Chart.js Integration -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('livewire:initialized', () => {
            const ctx = document.getElementById('formAnalyticsChart');
            const analyticsData = @json($analyticsData);

            if (ctx && analyticsData.labels.length > 0) {
                new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: analyticsData.labels,
                        datasets: [
                            {
                                label: 'Telah Disubmit',
                                data: analyticsData.submitted,
                                backgroundColor: 'rgba(54, 162, 235, 0.7)',
                                borderColor: 'rgba(54, 162, 235, 1)',
                                borderWidth: 1
                            },
                            {
                                label: 'Masih Draft',
                                data: analyticsData.draft,
                                backgroundColor: 'rgba(255, 99, 132, 0.7)',
                                borderColor: 'rgba(255, 99, 132, 1)',
                                borderWidth: 1
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: { stepSize: 1 }
                            }
                        }
                    }
                });
            }
        });
    </script>
</div>
