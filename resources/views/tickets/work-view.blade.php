<x-app-layout>

    <div class="container mt-4">
        <h4 class="mb-3">🎯 To-Do List Ticket Saya</h4>

        @forelse ($tickets as $ticket)
        <div class="list-group mb-2 shadow-sm">
            <div class="list-group-item d-flex justify-content-between align-items-start">
                <div class="ms-2 me-auto">
                    <div class="fw-bold">{{ $ticket->nomor_fuhd }}</div>
                    <small class="text-muted">
                        Menu: {{ $ticket->menu->name }} |
                        Deadline: {{ $ticket->ticketAssigns->first()->due_date ? \Carbon\Carbon::parse($ticket->ticketAssigns->first()->due_date)->format('d M Y') : '-'}} / {{ $ticket->ticketAssigns->first()?->due_time ? \Carbon\Carbon::parse($ticket->ticketAssigns->first()->due_time)->format('H:i') : '-' }}
                    </small>
                    <br>
                    <small>
                        Prioritas:
                        @php $priority = $ticket->ticketAssigns->first()?->priority?->id; @endphp
                        @if($priority == 1)
                        <span class="badge bg-danger">Urgent</span>
                        @elseif($priority == 2)
                        <span class="badge bg-orange text-dark">High</span>
                        @elseif($priority == 3)
                        <span class="badge bg-warning text-dark">Medium</span>
                        @else
                        <span class="badge bg-success">Low</span>
                        @endif

                        &nbsp;Status:
                        <span class="badge {{ $ticket->status == 'Assigned' ? 'bg-warning text-dark' : 'bg-primary' }}">
                            {{ $ticket->status }}
                        </span>
                    </small>
                </div>

                <div>
                    @if($ticket->status == 'Assigned')
                    <form action="{{ route('tickets.start', $ticket->id) }}" method="POST" class="d-inline">
                        @csrf
                        <button class="btn btn-sm btn-primary" data-bs-toggle="tooltip" title="Mulai Pengerjaan">
                            <i class="fa-solid fa-play"></i>
                        </button>
                    </form>
                    @endif

                    <button type="button"
                        class="btn btn-sm btn-danger btn-throw"
                        data-id="{{ $ticket->id }}"
                        data-bs-toggle="tooltip"
                        title="Alihkan">
                        <i class="fa-solid fa-share"></i>
                    </button>

                    @if($ticket->status != 'Completed' && $ticket->status != 'Closed' && $ticket->status != 'Assigned')
                    <button type="button"
                        class="btn btn-sm btn-success btn-complete"
                        data-id="{{ $ticket->id }}"
                        data-bs-toggle="tooltip"
                        title="Selesai">
                        <i class="fa-solid fa-check"></i>
                    </button>
                    @endif

                    <button type="button"
                        class="btn btn-sm btn-secondary btn-force-close"
                        data-id="{{ $ticket->id }}"
                        data-bs-toggle="tooltip"
                        title="Batalkan">
                        <i class="fa-solid fa-square-xmark"></i>
                    </button>

                    <form action="{{ route('tickets.show', $ticket->id) }}" method="GET" class="d-inline">
                        <button class="btn btn-sm btn-warning" data-bs-toggle="tooltip" title="Detail">
                            <i class="fa-solid fa-eye text-white"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>
        @empty
        <div class="alert alert-info">Tidak ada tiket yang perlu dikerjakan🎉</div>
        @endforelse

        <div class="d-flex justify-content-center mt-3">
            {{ $tickets->links('pagination::bootstrap-5') }}
        </div>
    </div>

    <div class="modal fade" id="completeModal" tabindex="-1" aria-labelledby="completeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <form id="completeForm" method="POST">
                @csrf
                <div class="modal-content">
                    <div class="modal-header bg-success text-white">
                        <h5 class="modal-title" id="completeModalLabel">Selesaikan Ticket</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p class="mb-2 text-sm text-muted">Tambahkan komentar penyelesaian sebelum menutup tiket ini.</p>
                        <div class="form-group">
                            <label for="completeComment" class="fw-semibold">Komentar Penyelesaian</label>
                            <textarea id="completeComment" name="comment" rows="3" class="form-control"
                                placeholder="Contoh: Ticket sudah diselesaikan dan diuji..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-success">
                            <i class="fa-solid fa-check"></i> Selesaikan
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="modal fade" id="throwModal" tabindex="-1" aria-labelledby="throwModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <form id="throwForm" method="POST">
                @csrf
                <div class="modal-content shadow-lg border-0 rounded-3">
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title" id="throwModalLabel">Alihkan Ticket</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p class="mb-3 text-sm text-muted">Tambahkan komentar atau alasan sebelum ticket dialihkan.</p>
                        <div class="form-group">
                            <label for="throwComment" class="fw-semibold">Komentar Pengalihan</label>
                            <textarea id="throwComment" name="comment" rows="5" class="form-control"
                                placeholder="Contoh: Ticket akan dialihkan ke pihak lain untuk ditindaklanjuti..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer d-flex justify-content-between">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-danger">
                            <i class="fa-solid fa-share"></i> Alihkan
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // Tooltip Bootstrap
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        tooltipTriggerList.map(function(el) {
            return new bootstrap.Tooltip(el)
        })

        const completeModal = new bootstrap.Modal(document.getElementById('completeModal'))
        const completeForm = document.getElementById('completeForm')

        document.querySelectorAll('.btn-complete').forEach(button => {
            button.addEventListener('click', function() {
                const ticketId = this.getAttribute('data-id')
                completeForm.action = `/tickets/${ticketId}/complete`
                completeModal.show()
            })
        })
        const throwModal = new bootstrap.Modal(document.getElementById('throwModal'))
        const throwForm = document.getElementById('throwForm')

        document.querySelectorAll('.btn-throw').forEach(button => {
            button.addEventListener('click', function() {
                const ticketId = this.getAttribute('data-id')
                throwForm.action = `/tickets/${ticketId}/throw`
                throwModal.show()
            })
        })
    </script>
</x-app-layout>