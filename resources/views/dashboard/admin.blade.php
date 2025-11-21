<x-app-layout>
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">
                👋 Selamat {{ now()->format('H') < 12 ? 'Pagi' : (now()->format('H') < 18 ? 'Siang' : 'Malam') }}, {{ auth()->user()->name }}
            </h1>
            <p class="text-sm text-gray-500" id="tanggal"></p>
        </div>
    </div>

    <!-- Statistik Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <x-dashboard-card color="from-indigo-500 to-indigo-600" icon="fa-ticket" title="Total Tiket" :value="$totalTickets" />
        <x-dashboard-card color="from-blue-500 to-blue-600" icon="fa-hourglass-half" title="Pending" :value="$pendingTickets" />
        <x-dashboard-card color="from-green-500 to-green-600" icon="fa-check-circle" title="Closed" :value="$closedTickets" />
        <x-dashboard-card color="from-yellow-500 to-yellow-600" icon="fa-users" title="Total Pengguna" :value="$totalUsers" />
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Chart -->
        <div class="col-span-2 bg-white p-6 rounded-2xl shadow">
            <h2 class="text-lg font-bold mb-4 text-gray-700">📈 Statistik Tiket Bulanan</h2>
            <form method="GET" action="{{ route('dashboard') }}" class="mb-4">
                <label for="tahun" class="font-semibold text-sm text-gray-700">Filter Tahun:</label>
                <select name="tahun" id="tahun" class="border rounded px-2 py-1 text-sm" onchange="this.form.submit()">
                    @for ($i = now()->year; $i >= 2020; $i--)
                    <option value="{{ $i }}" {{ $i == $tahun ? 'selected' : '' }}>{{ $i }}</option>
                    @endfor
                </select>
            </form>
            <canvas id="ticketChart" height="120"></canvas>
        </div>

        <!-- Aktivitas Terbaru -->
        <div class="bg-white p-6 rounded-2xl shadow">
            <h2 class="text-lg font-bold mb-4 text-gray-700">🧾 Aktivitas Terbaru</h2>
            <ul class="space-y-3 text-sm">
                @foreach ($recentTickets as $t)
                <li class="flex justify-between">
                    <span>{{ $t->nomor_fuhd ?? 'Tiket tanpa judul' }}</span>
                    @if ($t->status == 'Draft')
                    <span class="text-gray-400 text-xs">{{ $t->status }}</span>
                    @endif
                    <span class="text-gray-400 text-xs">{{ $t->updated_at->diffForHumans() }}</span>
                </li>
                @endforeach
            </ul>
        </div>
    </div>

    <!-- Chart -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        new Chart(document.getElementById('ticketChart').getContext('2d'), {
            type: 'line',
            data: {
                labels: @json($chartData['labels']),
                datasets: [{
                        label: 'Tiket Masuk',
                        data: @json($chartData['open']),
                        borderColor: '#4f46e5',
                        tension: 0.4
                    },
                    {
                        label: 'Tiket Selesai',
                        data: @json($chartData['closed']),
                        borderColor: '#16a34a',
                        tension: 0.4
                    },
                    {
                        label: 'Tiket Progress',
                        data: @json($chartData['progress']), // PERBAIKAN: Ubah dari 'In Progress' ke 'progress'
                        borderColor: '#f59e0b',
                        tension: 0.4
                    }
                ]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    }
                }
            }
        });

        // Clock
        setInterval(() => {
            const now = new Date();
            const hari = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
            const bulan = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
            document.getElementById('tanggal').textContent = `${hari[now.getDay()]}, ${now.getDate()} ${bulan[now.getMonth()]} ${now.getFullYear()}`;
        }, 1000);
    </script>
</x-app-layout>