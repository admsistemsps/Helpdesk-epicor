<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.datatables.net/2.3.4/css/dataTables.bootstrap5.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')

    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>
</head>

<body x-data="{ 
    sidebarOpen: false,
    get sidebarWidth() {
        if (window.innerWidth >= 768) {
            return this.sidebarOpen ? 224 : 64; // 224px open, 64px collapsed (icon only)
        }
        return 0; // Mobile: no width when closed
    }
}" class="bg-gray-100 font-sans antialiased">

    <!-- SIDEBAR OVERLAY (Mobile only) -->
    <div x-show="sidebarOpen"
        @click="sidebarOpen = false"
        class="fixed inset-0 bg-black/60 backdrop-blur-sm z-30 md:hidden"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0">
    </div>

    <!-- SIDEBAR -->
    <aside
        :class="sidebarOpen ? 'w-56' : 'w-0 md:w-16'"
        class="bg-gray-900 text-gray-300 min-h-screen h-full fixed top-0 left-0 flex flex-col
           transition-all duration-300 ease-in-out z-40 shadow-lg text-sm overflow-hidden"
        x-cloak>
        @include('layouts.sidebar')
    </aside>

    <!-- NAVBAR -->
    <nav
        :style="`left: ${sidebarWidth}px;`"
        class="fixed top-0 right-0 h-15 bg-white shadow flex items-center justify-between z-30
           transition-all duration-300 ease-in-out">
        @include('layouts.navbar')
    </nav>

    <!-- Main Content -->
    <div
        :style="`margin-left: ${sidebarWidth}px;`"
        class="pt-20 mb-4 px-4 md:pr-4 transition-all duration-300 ease-in-out">
        <main>
            @if (isset($slot))
            {{ $slot }}
            @else
            @yield('content')
            @endif
        </main>
    </div>

    @stack('scripts')
    <!-- ⚙️ Library scripts -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.datatables.net/2.3.4/js/dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/2.3.4/js/dataTables.bootstrap5.min.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <!-- Clock & Alert helper -->
    <script>
        document.addEventListener("DOMContentLoaded", function() {

            // 🔹 Global DataTable Initializer
            window.initDataTable = function(selector, options = {}) {
                const $table = $(selector);
                if (!$table.length) return;

                const defaultOptions = {
                    scrollX: true,
                    autoWidth: false,
                    dom: '<"dt-top flex justify-between items-center text-sm mb-3"l f>t<"dt-bottom flex justify-between text-sm items-center mt-3"ip>',
                    pageLength: 10,
                    lengthMenu: [5, 10, 25, 50, 100],
                    language: {
                        lengthMenu: "Tampilkan _MENU_ data",
                        search: "Cari:",
                        info: "Menampilkan _START_ - _END_ dari _TOTAL_ data",
                        infoEmpty: "Tidak ada data",
                        zeroRecords: "Data tidak ditemukan",
                        paginate: {
                            first: "Awal",
                            last: "Akhir",
                            next: "→",
                            previous: "←"
                        },
                    },
                    columnDefs: [{
                        targets: '_all',
                        className: 'text-center align-middle'
                    }],
                    initComplete: function() {
                        const wrapper = this.api().table().container();

                        // Styling select "Tampilkan"
                        $('div.dataTables_length select', wrapper)
                            .addClass('border border-gray-300 rounded-md px-2 py-1 text-sm focus:ring-1 focus:ring-purple-500 focus:outline-none')
                            .css({
                                'background-color': 'white',
                                'font-size': '0.875rem',
                                'height': '1.9rem',
                            });

                        // Styling input "Cari"
                        $('div.dataTables_filter input', wrapper)
                            .addClass('border border-gray-300 rounded-md px-2 py-1 ml-2 text-sm focus:ring-1 focus:ring-purple-500 focus:outline-none')
                            .attr('placeholder', 'Cari data...')
                            .css({
                                'font-size': '0.875rem',
                                'height': '1.9rem',
                                'width': '180px'
                            });

                        $('div.dataTables_length label, div.dataTables_filter label', wrapper)
                            .addClass('text-sm font-medium text-gray-700');
                    }
                };

                const finalOptions = $.extend(true, {}, defaultOptions, options);
                const table = $table.DataTable(finalOptions);

                // ✅ Pastikan elemen "search" dan "show data" diletakkan di luar scroll
                const $wrapper = $table.closest('.dataTables_wrapper');
                const $topBar = $wrapper.find('.dt-top');

                const $header = $('.flex.items-center.justify-between.mb-4').first();
                if ($header.length) {
                    $topBar.insertAfter($header);
                } else {
                    $wrapper.before($topBar);
                }

                return table;
            };

            // 🔹 Auto-inisialisasi semua tabel dengan class .datatable
            window.autoInitDataTables = function() {
                $('.datatable').each(function() {
                    const selector = this;
                    let customOptions = {};

                    // Jika ada konfigurasi kolom khusus di data-attribute
                    const columnsAttr = $(this).data('columns');
                    if (columnsAttr) {
                        try {
                            customOptions.columnDefs = JSON.parse(columnsAttr);
                        } catch (e) {
                            console.error("❌ Format JSON columnDefs salah di tabel:", selector);
                        }
                    }

                    initDataTable(selector, customOptions);
                });
            };

            // Jalankan otomatis saat halaman siap
            setTimeout(autoInitDataTables, 300);
        });

        function updateClock() {
            const now = new Date();
            const hours = now.getHours().toString().padStart(2, '0');
            const minutes = now.getMinutes().toString().padStart(2, '0');
            const clockEl = document.getElementById('clock');
            if (clockEl) {
                clockEl.textContent = `${hours}.${minutes} WIB`;
            }
        }
        setInterval(updateClock, 1000);
        updateClock();

        const swalBase = Swal.mixin({
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#6b7280',
            buttonsStyling: true,
            customClass: {
                confirmButton: 'px-4 py-2 rounded text-white bg-blue-600 hover:bg-blue-700',
                cancelButton: 'px-4 py-2 rounded text-white bg-gray-500 hover:bg-gray-600'
            }
        });

        function confirmAction(options = {}) {
            return swalBase.fire({
                title: options.title || 'Yakin?',
                text: options.text || 'Tindakan ini tidak bisa dibatalkan.',
                icon: options.icon || 'warning',
                showCancelButton: true,
                confirmButtonText: options.confirmButtonText || 'Ya, lanjutkan',
                cancelButtonText: options.cancelButtonText || 'Batal'
            });
        }

        function successAlert(message = 'Berhasil!') {
            return swalBase.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: message,
                timer: 2000,
                showConfirmButton: false
            });
        }

        function errorAlert(message = 'Terjadi kesalahan!') {
            return swalBase.fire({
                icon: 'error',
                title: 'Gagal!',
                text: message
            });
        }
    </script>

    @if(session('success'))
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'success',
                title: "{{ session('success') }}",
                showConfirmButton: false,
                timer: 2000,
                timerProgressBar: true
            });
        });
    </script>
    @endif

    @if(session('error'))
    <script>
        document.addEventListener("DOMContentLoading", function() {
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'error',
                title: "{{ session('error') }}",
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true
            });
        });
    </script>
    @endif
</body>

</html>