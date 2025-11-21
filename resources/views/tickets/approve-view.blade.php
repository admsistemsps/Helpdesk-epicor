<!-- tickets.index.blade.php - Simple DataTables (Vanilla JS) FULL CODE -->
<x-app-layout>
    <!-- Breadcrumb -->
    <x-breadcrumb />

    <!-- Wrapper card -->
    <div class="w-full bg-white shadow-md rounded-lg p-3 flex flex-col">
        <!-- Header + Toolbar -->
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-2xl font-bold">Approval Ticket</h2>
        </div>

        <!-- Table -->
        <div class="table-responsive">
            <table id="ticketTable" class="table table-striped table-bordered align-middle">
                <thead>
                    <tr>
                        <th scope="col" class="px-6 py-3">#</th>
                        <th scope="col" class="px-6 py-3">FUHD</th>
                        <th scope="col" class="px-6 py-3">Nomor</th>
                        <th scope="col" class="px-6 py-3">Menu</th>
                        <th scope="col" class="px-6 py-3">Sub-Menu</th>
                        <th scope="col" class="px-6 py-3">Status</th>
                        <th scope="col" class="px-6 py-3">Dibuat Oleh</th>
                        <th scope="col" class="px-6 py-3">Departemen</th>
                        <th scope="col" class="px-6 py-3">Tanggal Dibuat</th>
                        <th scope="col" class="px-6 py-3">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($tickets as $ticket)
                    <tr class="bg-white border-b hover:bg-gray-50">
                        <td class="px-6 py-4">{{ $loop->iteration }}</td>
                        <td class="px-6 py-4">{{ $ticket->nomor_fuhd }}</td>
                        <td class="px-6 py-4">{{ $ticket->nomor }}</td>
                        <td class="px-6 py-4">{{ $ticket->menu?->name ?? '-' }}</td>
                        <td class="px-6 py-4">{{ $ticket->submenu?->name ?? '-' }}</td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 rounded text-xs
                                @if($ticket->status == 'Menunggu') bg-yellow-100 text-yellow-700
                                @elseif($ticket->status == 'Assigned') bg-blue-100 text-blue-700
                                @elseif($ticket->status == 'Approved') bg-green-100 text-green-700
                                @elseif($ticket->status == 'Rejected') bg-red-100 text-red-700
                                @elseif($ticket->status == 'Closed') bg-red-100 text-red-700
                                @elseif($ticket->status == 'Draft') bg-blue-100 text-blue-700
                                @elseif($ticket->status == 'Completed') bg-green-300 text-green-700
                                @elseif($ticket->status == 'Feedback') bg-yellow-300 text-yellow-700
                                @else bg-gray-100 text-gray-700
                                @endif">
                                {{ ucfirst(str_replace('_', ' ', $ticket->status)) }}
                            </span>
                        </td>
                        <td class="px-6 py-4">{{ $ticket->user?->name ?? 'Unknown' }}</td>
                        <td class="px-6 py-4">
                            {{ $ticket->user->department?->code ?? 'Unknown' }}
                        </td>
                        <td class="px-6 py-4">{{ $ticket->created_at->format('d M Y H:i') }}</td>
                        <td class="px-6 py-4 gap-2 justify-center">

                            @php
                            $approver = auth()->user();
                            $approverPosition = $approver->position;
                            $canApprove = false;

                            if ($approverPosition && $ticket->status !== 'Draft' && $approver->id !== $ticket->requestor_id) {
                            if ($ticket->current_approval_position_id) {
                            $samePosition = $ticket->current_approval_position_id == $approverPosition->id;
                            $sameDivision = !$ticket->current_approval_division_id
                            || $ticket->current_approval_division_id == $approver->division_id;

                            if ($samePosition && $sameDivision) {
                            $canApprove = true;
                            }
                            } elseif ($ticket->current_approval_value == $approverPosition->level) {
                            $canApprove = true;
                            }
                            }
                            @endphp


                            @if($canApprove)
                            <form action="{{ route('tickets.approve', $ticket->id) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit"
                                    class="inline-flex items-center gap-2 bg-green-600 text-white text-xs px-3 py-2 rounded-lg shadow hover:bg-green-700 transition btn-approve">
                                    <i class="fa-solid fa-check"></i>
                                </button>
                            </form>
                            <button type="button"
                                class="inline-flex items-center gap-2 bg-red-600 text-white text-xs px-3 py-2 rounded-lg shadow hover:bg-red-700 transition"
                                onclick="openRejectModal({{ $ticket->id }})">
                                <i class="fa-solid fa-xmark"></i>
                            </button>
                            @endif

                            <a href="{{ route('tickets.show', $ticket->slug) }}"
                                class="inline-flex items-center gap-2 bg-blue-600 text-white text-xs px-3 py-2 rounded-lg shadow hover:bg-blue-700 transition">
                                <i class="fa-solid fa-eye"></i>
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!--Komentar Penolakan -->
        <div id="rejectModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
            <div class="bg-white rounded-lg shadow-lg w-full max-w-md p-6 relative">
                <h2 class="text-lg font-semibold text-gray-800 mb-3">Tolak Tiket</h2>
                <p class="text-sm text-gray-600 mb-3">Berikan alasan mengapa tiket ini ditolak:</p>

                <form id="rejectForm" method="POST">
                    @csrf
                    <textarea name="comment" id="rejectNote" rows="4" required
                        class="w-full text-sm border border-gray-300 rounded-md p-2 focus:ring focus:ring-red-200 focus:border-red-400"
                        placeholder="Tuliskan alasan penolakan di sini..."></textarea>

                    <div class="flex justify-end mt-4 gap-2">
                        <button type="button"
                            onclick="closeRejectModal()"
                            class="px-3 py-2 text-sm bg-gray-300 rounded-md hover:bg-gray-400 transition">
                            Batal
                        </button>
                        <button type="submit"
                            class="px-3 py-2 text-sm bg-red-600 text-white rounded-md hover:bg-red-700 transition">
                            Kirim Penolakan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
    <!-- Simple DataTables Library CSS & JS -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.datatables.net/2.0.8/js/dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/2.0.8/js/dataTables.bootstrap5.min.js"></script>
    <script src="//unpkg.com/alpinejs" defer></script>

    <script>
        let currentTicketId = null;

        function openRejectModal(ticketId) {
            currentTicketId = ticketId;
            const modal = document.getElementById('rejectModal');
            const form = document.getElementById('rejectForm');

            form.action = `/tickets/${ticketId}/reject`;
            modal.classList.remove('hidden');
        }

        function closeRejectModal() {
            const modal = document.getElementById('rejectModal');
            modal.classList.add('hidden');
            document.getElementById('rejectNote').value = '';
        }

        // Tutup modal jika klik area luar
        window.addEventListener('click', function(e) {
            const modal = document.getElementById('rejectModal');
            if (e.target === modal) {
                closeRejectModal();
            }
        });

        document.querySelectorAll('.btn-approve').forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                const form = this.closest('form'); // ambil form induk tombol
                const url = form.action; // ambil URL action form

                confirmAction({
                    title: 'Approve Tiket?',
                    text: 'Apakah kamu yakin ingin menyetujui tiket ini?',
                    icon: 'question',
                    confirmButtonText: 'Ya, Approve!',
                    cancelButtonText: 'Batal',
                    confirmColor: '#16a34a' // Tailwind green-600
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit(); // kirim form, bukan redirect ke URL
                    }
                });
            });
        });

        $(document).ready(function() {
            $('#ticketTable').DataTable({
                autoWidth: false,
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
                }]
            });
        });
    </script>
    @endpush
</x-app-layout>