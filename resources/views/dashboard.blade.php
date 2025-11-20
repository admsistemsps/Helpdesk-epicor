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

    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Chart -->
        <div class="col-span-2 bg-white p-6 rounded-2xl shadow">
            <h2 class="text-lg font-bold mb-4 text-gray-700">📈 Statistik Tiket Bulanan</h2>
            <form method="GET" action="{{ route('dashboard') }}" class="mb-4">
                <label for="tahun" class="font-semibold text-sm text-gray-700">Filter Tahun:</label>
                <select name="tahun" id="tahun" class="border rounded px-2 py-1 text-sm" onchange="this.form.submit()">

                </select>
            </form>
            <canvas id="ticketChart" height="120"></canvas>
        </div>

        <!-- Aktivitas Terbaru -->
        <div class="bg-white p-6 rounded-2xl shadow">
            <h2 class="text-lg font-bold mb-4 text-gray-700">🧾 Aktivitas Terbaru</h2>
            <ul class="space-y-3 text-sm">

            </ul>
        </div>
    </div>

    <!-- Chart -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        
    </script>
</x-app-layout>