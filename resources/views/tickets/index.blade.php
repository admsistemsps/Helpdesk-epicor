<x-app-layout>
    <x-breadcrumb />

    <div class="w-full bg-white shadow-md rounded-lg p-3 flex flex-col">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-2xl font-bold">Tracking Ticket</h2>
        </div>

        <div class="dt-top-controls flex justify-between items-center mb-3"></div>
        <div class="table-responsive">
            <table id="ticketTable" class="datatable table table-striped table-bordered align-middle text-sm">
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
                        <th scope="col" class="px-6 py-3">Tanggal Ditutup</th>
                        @if(auth()->user()->role_id == 2)
                        <th scope="col" class="px-6 py-3">Diselesaikan</th>
                        @endif
                        <th scope="col" class="px-6 py-3">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($tickets as $ticket)
                    <tr class="bg-white border-b hover:bg-gray-50">
                        <td class="px-6 py-2">{{ $loop->iteration }}</td>
                        <td class="px-6 py-2">{{ $ticket->nomor_fuhd }}</td>
                        <td class="px-6 py-2">{{ $ticket->nomor }}</td>
                        <td class="px-6 py-2">{{ $ticket->menu?->name ?? '-' }}</td>
                        <td class="px-6 py-2">{{ $ticket->submenu?->name ?? '-' }}</td>
                        <td class="px-6 py-2">
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
                        <td class="px-6 py-2">{{ $ticket->user?->name ?? 'Unknown' }}</td>
                        <td class="px-6 py-2">
                            {{ $ticket->user->department?->code ?? 'Unknown' }}
                        </td>
                        <td class="px-6 py-2">{{ $ticket->created_at->format('d M Y H:i') }}</td>
                        <td class="px-6 py-2">{{ $ticket->closed_date ? \Carbon\Carbon::parse($ticket->closed_date)->format('d M Y') : '-' }}</td>

                        @if(auth()->user()->role_id == 2)
                        <td class="px-6 py-2">
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

                        <td class="px-6 py-2 gap-2 justify-center">
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
                confirmButtonColor: '#f59e0b',
                cancelButtonColor: '#6b7280',
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

        window.addEventListener('click', function(e) {
            const modal = document.getElementById('rejectModal');
            if (e.target === modal) {
                closeRejectModal();
            }
        });

        document.querySelectorAll('.btn-approve').forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                const form = this.closest('form');
                const url = form.action;

                confirmAction({
                    title: 'Approve Tiket?',
                    text: 'Apakah kamu yakin ingin menyetujui tiket ini?',
                    icon: 'question',
                    confirmButtonText: 'Ya, Approve!',
                    cancelButtonText: 'Batal',
                    confirmColor: '#16a34a'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        });

    </script>
    @if(session('success'))
    <script>
        localStorage.removeItem('ticket-header');
        localStorage.removeItem('ticket-details');
    </script>
    @endif
    @endpush
</x-app-layout>