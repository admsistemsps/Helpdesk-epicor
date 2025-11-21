<x-app-layout>
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">
                👋 Hai, {{ auth()->user()->name }}
            </h1>
            <p class="text-sm text-gray-500" id="tanggal"></p>
        </div>
    </div>

    <!-- Statistik Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <x-dashboard-card color="from-indigo-500 to-indigo-600" icon="fa-ticket" title="Total Tiket Saya" :value="$totalTickets" />
        <x-dashboard-card color="from-yellow-500 to-yellow-600" icon="fa-hourglass-start" title="Open" :value="$openTickets" />
        <x-dashboard-card color="from-blue-500 to-blue-600" icon="fa-spinner" title="In Progress" :value="$inProgressTickets" />
        <x-dashboard-card color="from-green-500 to-green-600" icon="fa-check" title="Closed" :value="$closedTickets" />
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Chart -->
        <div class="col-span-2 bg-white p-6 rounded-2xl shadow">
            <h2 class="text-lg font-bold mb-4 text-gray-700">📊 Tiket Saya per Bulan</h2>
            <canvas id="userChart" height="120"></canvas>
        </div>

        <!-- Riwayat Tiket -->
        <div class="bg-white p-6 rounded-2xl shadow">
            <h2 class="text-lg font-bold mb-4 text-gray-700">🕓 Tiket Terbaru</h2>
            <ul class="space-y-3 text-sm">
                @foreach ($myRecentTickets as $t)
                <li class="flex justify-between">
                    <span>{{ $t->nomor_fuhd }}</span>
                    <span class="text-gray-400 text-xs">{{ $t->status }}</span>
                </li>
                @endforeach
            </ul>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        new Chart(document.getElementById('userChart').getContext('2d'), {
            type: 'bar',
            data: {
                labels: @json($chartData['labels']),
                datasets: [{
                    label: 'Tiket Saya',
                    data: @json($chartData['myTickets']),
                    backgroundColor: '#4f46e5'
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: false
                    }
                }
            }
        });

        setInterval(() => {
            const now = new Date();
            const hari = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
            const bulan = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
            document.getElementById('tanggal').textContent = `${hari[now.getDay()]}, ${now.getDate()} ${bulan[now.getMonth()]} ${now.getFullYear()}`;
        }, 1000);
    </script>
</x-app-layout>