<!-- tickets.index.blade.php - Simple DataTables (Vanilla JS) FULL CODE -->
<x-app-layout>
    <!-- Breadcrumb -->
    <x-breadcrumb />

    <!-- Wrapper card -->
    <div class="w-full bg-white shadow-md rounded-lg p-3 flex flex-col">
        <!-- Header + Toolbar -->
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-2xl font-bold">Tracking Ticket</h2>
            <a href="{{ route('tickets.create') }}"
                class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-purple-600 rounded-lg hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-purple-500">
                <i class="fa-solid fa-plus mr-2"></i> Tambah Ticket
            </a>
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
                        <th scope="col" class="px-6 py-3">Tanggal Selesai Dikerjakan</th>
                        <th scope="col" class="px-6 py-3">Tanggal Ditutup</th>
                        @if(auth()->user()->role_id == 2)
                        <th scope="col" class="px-6 py-3">Diselesaikan</th>
                        @endif
                        @if(auth()->user()->role_id == 1)
                        <th scope="col" class="px-6 py-3">Assign To</th>
                        @endif
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
                        <td class="px-6 py-4">
                            {{$ticket->finish_date ? \Carbon\Carbon::parse($ticket->finish_date)->format('d M Y') : '-' }}
                        </td>
                        <td class="px-6 py-4">{{ $ticket->closed_date ? \Carbon\Carbon::parse($ticket->closed_date)->format('d M Y') : '-' }}</td>

                        @if(auth()->user()->role_id == 2)
                        <td class="px-6 py-4">
                            @if($ticket->status == 'Assigned' || $ticket->status == 'Feedback')
                            <button type="button"
                                class="inline-flex items-center gap-2 bg-purple-600 text-white px-4 py-2 rounded-lg shadow hover:bg-purple-700 transition"
                                onclick="openCommentModal({{ $ticket->id }})">
                                <i class="fa-solid fa-user-check"></i>
                            </button>
                            @elseif($ticket->status == 'Completed')
                            <span
                                class="inline-flex items-center gap-2 bg-green-600 text-white px-4 py-2 rounded-lg shadow cursor-default">
                                <i class="fa-solid fa-check"></i>
                            </span>
                            @elseif($ticket->status == 'Closed')
                            <span
                                class="inline-flex items-center gap-2 bg-red-600 text-white px-4 py-2 rounded-lg shadow cursor-default">
                                <i class="fa-solid fa-close"></i>
                            </span>
                            @endif
                        </td>
                        @endif

                        @if(auth()->user()->role_id == 1)
                        <td class="px-6 py-4">
                            @if(!$ticket->assigned_to)
                            <span class="badge bg-green-500">
                                {{ $ticket->assignUser?->name ?? 'N/A' }}
                            </span>
                            @else
                            <span class="text-muted">-</span>
                            @endif
                        </td>
                        @endif
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

                            <form action="{{ route('tickets.reject', $ticket->id) }}" method="POST" class="inline">
                                @csrf
                                <button type="button"
                                    class="inline-flex items-center gap-2 bg-red-600 text-white text-xs px-3 py-2 rounded-lg shadow hover:bg-red-700 transition"
                                    onclick="openRejectModal({{ $ticket->id }})">
                                    <i class="fa-solid fa-xmark"></i>
                                </button>
                            </form>
                            @endif


                            <a href="{{ route('tickets.show', $ticket->slug) }}"
                                class="inline-flex items-center gap-2 bg-blue-600 text-white text-xs px-3 py-2 rounded-lg shadow hover:bg-blue-700 transition">
                                <i class="fa-solid fa-eye"></i>
                            </a>

                            @if(auth()->id() === $ticket->requestor_id && $ticket->status === 'Completed')
                            <button type="button"
                                class="inline-flex items-center gap-2 bg-yellow-500 text-white text-xs px-4 py-2 rounded-lg shadow hover:bg-yellow-600 transition"
                                onclick="feedbackTicket({{ $ticket->id }})">
                                <i class="fa-solid fa-comment-dots"></i>
                            </button>

                            <button type="button"
                                class="inline-flex items-center gap-2 bg-green-600 text-white text-xs px-4 py-2 rounded-lg shadow hover:bg-green-700 transition"
                                onclick="closeTicket({{ $ticket->id }})">
                                <i class="fa-solid fa-lock"></i>
                            </button>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <!-- Modal -->
        <!-- Komentar Penyelesaian -->
        <div id="commentModal"
            class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
            <div class="bg-white w-full max-w-md rounded-lg shadow-lg p-6 relative">
                <h2 class="text-lg font-semibold mb-3">Komentar Penyelesaian</h2>

                <form id="commentForm" method="POST">
                    @csrf
                    <textarea name="comment" rows="4" required
                        class="w-full border rounded px-3 py-2 mb-4 focus:outline-none focus:ring focus:border-blue-300"
                        placeholder="Tuliskan komentar penyelesaian..."></textarea>

                    <div class="flex justify-end gap-2">
                        <button type="button" onclick="closeCommentModal()"
                            class="px-4 py-2 bg-gray-400 text-white rounded hover:bg-gray-500 transition">
                            Batal
                        </button>
                        <button type="submit"
                            class="px-4 py-2 bg-purple-600 text-white rounded hover:bg-purple-700 transition">
                            Kirim
                        </button>
                    </div>
                </form>

                <!-- Tombol close pojok kanan -->
                <button onclick="closeCommentModal()"
                    class="absolute top-3 right-3 text-gray-500 hover:text-gray-700">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
        </div>

        <!--Komentar Penolakan -->
        <div id="rejectModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
            <div class="bg-white rounded-lg shadow-lg w-full max-w-md p-6 relative">
                <h2 class="text-lg font-semibold text-gray-800 mb-3">Tolak Tiket</h2>
                <p class="text-sm text-gray-600 mb-3">Berikan alasan mengapa tiket ini ditolak:</p>

                <form id="rejectForm" method="POST">
                    @csrf
                    <textarea name="note_reject" id="rejectNote" rows="4" required
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
        // Feedback Ticket
        function feedbackTicket(ticketId) {
            Swal.fire({
                title: 'Kirim Feedback',
                input: 'textarea',
                inputLabel: 'Tuliskan revisi atau masukan Anda',
                inputPlaceholder: 'Masukkan komentar feedback di sini...',
                inputAttributes: {
                    'aria-label': 'Komentar Feedback'
                },
                showCancelButton: true,
                confirmButtonText: 'Kirim',
                cancelButtonText: 'Batal',
                confirmButtonColor: '#f59e0b', // kuning
                cancelButtonColor: '#6b7280', // abu
                preConfirm: (comment) => {
                    if (!comment) {
                        Swal.showValidationMessage('Komentar tidak boleh kosong!');
                        return false;
                    }
                    return comment;
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch(`/tickets/${ticketId}/feedback`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({
                                feedback_comment: result.value
                            })
                        })
                        .then(res => {
                            if (!res.ok) throw new Error('Gagal kirim feedback');
                            return res.json ? res.json() : res.text();
                        })
                        .then(() => {
                            Swal.fire({
                                icon: 'success',
                                title: 'Feedback dikirim!',
                                text: 'Ticket telah dikembalikan untuk revisi.',
                                confirmButtonColor: '#3085d6'
                            }).then(() => location.reload());
                        })
                        .catch(() => {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal',
                                text: 'Terjadi kesalahan saat mengirim feedback.'
                            });
                        });
                }
            });
        }

        // Close Ticket
        function closeTicket(ticketId) {
            Swal.fire({
                title: 'Tutup Ticket?',
                text: 'Setelah ditutup, ticket tidak dapat direvisi lagi.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, Tutup',
                cancelButtonText: 'Batal',
                confirmButtonColor: '#16a34a',
                cancelButtonColor: '#6b7280',
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch(`/tickets/${ticketId}/close`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            }
                        })
                        .then(res => {
                            if (!res.ok) throw new Error('Gagal menutup ticket');
                            return res.json ? res.json() : res.text();
                        })
                        .then(() => {
                            Swal.fire({
                                icon: 'success',
                                title: 'Ticket Ditutup',
                                text: 'Ticket berhasil ditandai sebagai Closed.',
                                confirmButtonColor: '#3085d6'
                            }).then(() => location.reload());
                        })
                        .catch(() => {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal',
                                text: 'Terjadi kesalahan saat menutup ticket.'
                            });
                        });
                }
            });
        }

        function openCommentModal(ticketId) {
            const modal = document.getElementById('commentModal');
            const form = document.getElementById('commentForm');
            form.action = `/tickets/${ticketId}/complete`; // route ke controller “complete”
            modal.classList.remove('hidden');
        }

        function closeCommentModal() {
            const modal = document.getElementById('commentModal');
            modal.classList.add('hidden');
        }

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