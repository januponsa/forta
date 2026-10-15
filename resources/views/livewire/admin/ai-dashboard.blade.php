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
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6" wire:ignore>
            <h3 class="text-lg font-medium text-gray-900 mb-4">Statistik Pengajuan Form</h3>
            <canvas id="formAnalyticsChart" height="200"></canvas>
        </div>
        
        <!-- AI Predictive Insights & Risk Distribution Card -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center gap-2.5">
                        <div class="p-2 bg-purple-100 text-purple-700 rounded-lg">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 leading-tight">AI Predictive Insights</h3>
                            <p class="text-xs text-gray-500">Prediksi keterlambatan & distribusi risiko kelulusan</p>
                        </div>
                    </div>
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-emerald-50 text-emerald-700 border border-emerald-200">
                        <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                        AI Engine Aktif
                    </span>
                </div>

                <!-- 3 Quick KPI Stat Pills -->
                <div class="grid grid-cols-3 gap-2 mb-4">
                    <div class="bg-red-50 border border-red-100 rounded-lg p-2.5 text-center">
                        <div class="text-[11px] font-medium text-red-600">Risiko Tinggi</div>
                        <div class="text-xl font-bold text-red-700 mt-0.5">{{ $predictiveInsights['highRiskCount'] ?? 0 }}</div>
                        <div class="text-[10px] text-red-500">Butuh intervensi</div>
                    </div>
                    <div class="bg-amber-50 border border-amber-100 rounded-lg p-2.5 text-center">
                        <div class="text-[11px] font-medium text-amber-600">Risiko Sedang</div>
                        <div class="text-xl font-bold text-amber-700 mt-0.5">{{ $predictiveInsights['mediumRiskCount'] ?? 0 }}</div>
                        <div class="text-[10px] text-amber-500">Dalam pantauan</div>
                    </div>
                    <div class="bg-emerald-50 border border-emerald-100 rounded-lg p-2.5 text-center">
                        <div class="text-[11px] font-medium text-emerald-600">On-Track (Aman)</div>
                        <div class="text-xl font-bold text-emerald-700 mt-0.5">{{ $predictiveInsights['onTrackRate'] ?? 100 }}%</div>
                        <div class="text-[10px] text-emerald-600">{{ $predictiveInsights['safeCount'] ?? 0 }} mhs</div>
                    </div>
                </div>

                <!-- Doughnut Chart & Recommendations Grid -->
                <div class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-center">
                    <!-- Chart Column -->
                    <div class="sm:col-span-5 flex flex-col items-center justify-center" wire:ignore>
                        <div class="relative w-32 h-32">
                            <canvas id="riskDistributionChart"></canvas>
                        </div>
                        <span class="text-[10px] text-gray-500 mt-1 font-medium">Distribusi Risiko Mhs</span>
                    </div>

                    <!-- AI Key Recommendations Column -->
                    <div class="sm:col-span-7 space-y-2">
                        @forelse(($predictiveInsights['recommendations'] ?? []) as $rec)
                            <div class="p-2 rounded-lg text-xs border {{ $rec['type'] === 'danger' ? 'bg-red-50/70 border-red-200 text-red-900' : ($rec['type'] === 'warning' ? 'bg-amber-50/70 border-amber-200 text-amber-900' : 'bg-blue-50/70 border-blue-200 text-blue-900') }}">
                                <div class="flex items-center gap-1.5 font-semibold text-[11px]">
                                    @if($rec['type'] === 'danger')
                                        <svg class="w-3.5 h-3.5 text-red-600 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                                    @elseif($rec['type'] === 'warning')
                                        <svg class="w-3.5 h-3.5 text-amber-600 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/></svg>
                                    @else
                                        <svg class="w-3.5 h-3.5 text-blue-600 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/></svg>
                                    @endif
                                    <span>{{ $rec['title'] }}</span>
                                </div>
                                <p class="mt-0.5 text-gray-600 leading-snug text-[10.5px]">{{ $rec['text'] }}</p>
                            </div>
                        @empty
                            <div class="text-center py-4 text-xs text-gray-500">
                                Seluruh mahasiswa saat ini berada dalam status aman (On-Track).
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            <div class="mt-3 pt-2.5 border-t border-gray-100 flex items-center justify-between text-xs text-gray-500">
                <span>Model: Analisis inaktivitas & histori penolakan</span>
                <a href="#at-risk-table" class="text-indigo-600 hover:text-indigo-800 font-medium inline-flex items-center gap-1">
                    Lihat {{ count($atRiskStudents) }} Mahasiswa Berisiko
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </a>
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

    <div id="at-risk-table" class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden mb-6 scroll-mt-6">
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
                                <button wire:click="sendAiReminder({{ $item['student']->id }})" wire:loading.attr="disabled" class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50">
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
        function initFormAnalyticsChart() {
            const ctx = document.getElementById('formAnalyticsChart');
            const analyticsData = @json($analyticsData);

            if (ctx && typeof Chart !== 'undefined' && analyticsData.labels && analyticsData.labels.length > 0) {
                if (window.formAnalyticsChartInstance) {
                    window.formAnalyticsChartInstance.destroy();
                }

                window.formAnalyticsChartInstance = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: analyticsData.labels,
                        datasets: [
                            {
                                label: 'Telah Disubmit',
                                data: analyticsData.submitted,
                                backgroundColor: 'rgba(54, 162, 235, 0.75)',
                                borderColor: 'rgba(54, 162, 235, 1)',
                                borderWidth: 1,
                                borderRadius: 4
                            },
                            {
                                label: 'Masih Draft',
                                data: analyticsData.draft,
                                backgroundColor: 'rgba(255, 99, 132, 0.75)',
                                borderColor: 'rgba(255, 99, 132, 1)',
                                borderWidth: 1,
                                borderRadius: 4
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: true,
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: { stepSize: 1 }
                            }
                        },
                        plugins: {
                            legend: {
                                position: 'top',
                            }
                        }
                    }
                });
            }
        }

        function initRiskDistributionChart() {
            const ctx = document.getElementById('riskDistributionChart');
            const chartData = @json($predictiveInsights['chart'] ?? null);

            if (ctx && typeof Chart !== 'undefined' && chartData && chartData.data) {
                if (window.riskChartInstance) {
                    window.riskChartInstance.destroy();
                }

                window.riskChartInstance = new Chart(ctx, {
                    type: 'doughnut',
                    data: {
                        labels: chartData.labels,
                        datasets: [{
                            data: chartData.data,
                            backgroundColor: chartData.colors,
                            borderWidth: 2,
                            borderColor: '#ffffff',
                            hoverOffset: 3
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        const label = context.label || '';
                                        const value = context.parsed || 0;
                                        const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                        const percentage = total > 0 ? Math.round((value / total) * 100) : 0;
                                        return `${label}: ${value} mhs (${percentage}%)`;
                                    }
                                }
                            }
                        },
                        cutout: '68%'
                    }
                });
            }
        }

        function initAllCharts() {
            initFormAnalyticsChart();
            initRiskDistributionChart();
        }

        document.addEventListener('livewire:initialized', initAllCharts);
        document.addEventListener('DOMContentLoaded', initAllCharts);
    </script>
</div>
