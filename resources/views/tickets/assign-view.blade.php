<x-app-layout>
    <x-breadcrumb />

    @forelse ($tickets as $ticket)
    <div>
        <div class="list-group mb-2 shadow-sm">
            <div class="list-group-item d-flex justify-content-between align-items-start">
                <div class="ms-2 me-auto">
                    <div class="fw-bold">{{ $ticket->nomor_fuhd }}</div>
                    <small class="text-muted">
                        Menu: {{ $ticket->menu->name }} |
                        Deadline: {{ $ticket->ticketAssigns->first()->due_date ?? '-' }} / {{ $ticket->ticketAssigns->first()->due_time ?? '-' }}
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
                        @elseif($priority == 4)
                        <span class="badge bg-success text-dark">Low</span>
                        @else
                        <span class="badge bg-secondary">N/A</span>
                        @endif

                        &nbsp;Status:
                        <span class="badge {{ $ticket->status == 'Assigned' ? 'bg-warning text-dark' : 'bg-primary' }}">
                            {{ $ticket->status }}
                        </span>
                    </small>
                </div>

                <div>
                    @if($ticket->status == 'Approved' || $ticket->status == 'Dialihkan')
                    <a href="{{ route('tickets.assign', $ticket->id)}}"
                        class="btn btn-sm text-white"
                        style="background:#4f46e5;border:none;"
                        onmouseover="this.style.background='#4338ca'"
                        onmouseout="this.style.background='#4f46e5'"
                        data-bs-toggle="tooltip"
                        title="Assign Ticket">
                        <i class="fa-solid fa-user-check"></i>
                    </a>
                    @endif

                    <form action="{{ route('tickets.show', $ticket->id) }}" method="GET" class="d-inline">
                        <button class="btn btn-sm btn-warning" data-bs-toggle="tooltip" title="Detail">
                            <i class="fa-solid fa-eye text-white"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @empty
    <div class="text-center">
        <h4 class="text-muted">Tidak ada data</h4>
    </div>
    @endforelse
</x-app-layout>