<x-app-layout>
    <x-breadcrumb />

    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">
                Assign Ticket: <span class="fw-bold">{{ $ticket->nomor_fuhd }}</span>
            </h5>
        </div>

        <div class="card-body">
            <form action="{{ route('tickets.assign.store', $ticket->id) }}" method="POST">
                @csrf

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="assigned_to" class="form-label fw-semibold">Pilih Pihak Penangan</label>
                        <select name="assigned_to" id="assigned_to" class="form-select" required>
                            <option value="">-- Pilih Pihak Penanganan --</option>

                            <optgroup label="Admin Sistem">
                                @foreach($admins as $admin)
                                <option value="{{ $admin->id }}">{{ $admin->name }}</option>
                                @endforeach
                            </optgroup>

                            <optgroup label="Konsultan">
                                <option value="CONSULTANT">Prismatech (Konsultan)</option>
                            </optgroup>
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label for="priority_id" class="form-label fw-semibold">Tingkat Prioritas</label>
                        <select name="priority_id" id="priority_id" class="form-select" required>
                            <option value="">-- Pilih Tingkatan --</option>
                            @foreach($priorities as $priority)
                            <option value="{{ $priority->id }}" data-sla="{{ $priority->sla_hours }}">
                                {{ $priority->name }} ({{ $priority->sla_hours }} Jam)
                            </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-12">
                        <label for="comment" class="form-label fw-semibold">Komentar</label>
                        <textarea name="comment" id="comment" class="form-control" placeholder="Tambahkan catatan..." required></textarea>
                    </div>
                </div>

                <hr>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="due_date" class="form-label fw-semibold">Tanggal Jatuh Tempo (SLA)</label>
                        <input type="date" name="due_date" id="due_date" class="form-control" readonly>
                    </div>

                    <div class="col-md-6">
                        <label for="due_time" class="form-label fw-semibold">Waktu Jatuh Tempo (SLA)</label>
                        <input type="time" name="due_time" id="due_time" class="form-control" readonly>
                    </div>
                </div>

                <div class="mt-4 d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="fa-solid fa-paper-plane me-2"></i>Assign
                    </button>

                    <a href="{{ route('tickets.index') }}" class="btn btn-secondary">
                        <i class="fa-solid fa-arrow-left me-2"></i>Batal
                    </a>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const prioritySelect = document.getElementById('priority_id');
            const dueDateInput = document.getElementById('due_date');
            const dueTimeInput = document.getElementById('due_time');

            prioritySelect.addEventListener('change', () => {
                const selected = prioritySelect.options[prioritySelect.selectedIndex];
                const slaHours = selected.getAttribute('data-sla');

                if (slaHours) {
                    const now = new Date();
                    now.setHours(now.getHours() + parseInt(slaHours));

                    // Format tanggal & waktu untuk input
                    const year = now.getFullYear();
                    const month = String(now.getMonth() + 1).padStart(2, '0');
                    const day = String(now.getDate()).padStart(2, '0');
                    const hours = String(now.getHours()).padStart(2, '0');
                    const minutes = String(now.getMinutes()).padStart(2, '0');

                    dueDateInput.value = `${year}-${month}-${day}`;
                    dueTimeInput.value = `${hours}:${minutes}`;
                } else {
                    dueDateInput.value = '';
                    dueTimeInput.value = '';
                }
            });
        });
    </script>
</x-app-layout>