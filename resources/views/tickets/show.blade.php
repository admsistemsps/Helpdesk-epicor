<x-app-layout>
    <x-breadcrumb />
    @push('scripts')
    <script>
        window.ticketDetails = @json($details ?? []);
        window.ticketStatus = "{{ strtolower($ticket->status ?? 'draft') }}";
    </script>
    @endpush
    <div x-data="ticketForm(window.ticketDetails, window.ticketStatus)" x-init="init()">

        <div class="max-w-auto mx-auto bg-white shadow-md rounded-lg p-6">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-bold mb-4">Track Ticket</h2>
                <div class="flex items-center px-4 py-2 rounded-md space-x-2"> <span class="px-2.5 py-1 rounded text-xs font-semibold @if($ticket->status == 'Menunggu') bg-yellow-100 text-yellow-700 @elseif($ticket->status == 'Assigned') bg-sky-100 text-sky-700 @elseif($ticket->status == 'Approved') bg-green-100 text-green-700 @elseif($ticket->status == 'Rejected') bg-red-100 text-red-700 @elseif($ticket->status == 'Draft') bg-blue-200 text-blue-700 @else bg-yellow-100 text-yellow-700 @endif"> {{ ucfirst(str_replace('_', ' ', $ticket->status)) }} </span> </div>
            </div>

            @if ($errors->any())
            <div class="bg-red-100 text-red-600 p-3 rounded mb-4">
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <form id="ticketForm" action="{{ route('tickets.update', $ticket) }}" method="POST" enctype="multipart/form-data"> @method('PUT') @csrf <!-- Tabs -->
                <div class="border-b mb-4 flex">
                    <button type="button" class="tab-btn active-tab" data-tab="tab-form">Header</button>
                    <button type="button" class="tab-btn" data-tab="tab-detail">Detail</button>
                    <button type="button" class="tab-btn" data-tab="tab-attachment">Attachment</button>
                    <button type="button" class="tab-btn" data-tab="tab-log">Log</button>
                </div> <!-- Tab Header -->
                <div id="tab-form" class="tab-content block">
                    <div class="mb-4">
                        <label class="block font-semibold mb-2">Nomor FUHD</label>
                        <input type="text" name="nomor_fuhd" value="{{ $ticket->nomor_fuhd }}" readonly class="w-full border rounded px-3 py-2 bg-gray-200 border-gray-300">
                    </div>
                    @php
                    $disabled = !in_array($ticket->status, ['Draft','Rejected']) ? 'disabled' : '';
                    @endphp

                    <div class="mb-3">
                        <label for="menu_id" class="form-label">Menu</label>
                        <select id="menu_id" name="menu_id" class="form-select" {{ $disabled }}>
                            @foreach($menus as $menu)
                            <option value="{{ $menu->id }}" {{ $menu->id == $ticket->menu_id ? 'selected' : '' }}>
                                {{ $menu->name }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="sub_menu_id" class="form-label">Sub Menu</label>
                        <select id="sub_menu_id" name="sub_menu_id" class="form-select" {{ $disabled }}>
                            @foreach($subMenus->where('menu_id', $ticket->menu_id) as $sub)
                            <option value="{{ $sub->id }}" {{ $sub->id == $ticket->sub_menu_id ? 'selected' : '' }}>
                                {{ $sub->name }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Tab Detail -->
                <div id="tab-detail" class="tab-content hidden">
                    <div class="mb-4 flex items-center justify-between">
                        <div>
                            <h3 class="text-lg font-semibold">Detail Ticket</h3>
                            <template x-if="details[activeIndex]?.id">
                                <span class="text-sm text-gray-500 ml-2">#<span x-text="details[activeIndex].ticket_line"></span></span>
                            </template>
                            <p class="text-sm text-gray-500">Isi satu per satu. Gunakan tombol +Tambah untuk menambah line baru.</p>
                        </div>
                        <div class="flex items-center gap-2">
                            <button type="button" @click="addLine()" :hidden="!isEditable" class="bg-green-600 text-white px-3 py-1 rounded hover:bg-green-700">
                                <i class="fa-solid fa-plus"></i> Tambah Line
                            </button>
                            <span class="text-sm text-gray-600">Line</span> <select x-model.number="activeIndex" @change="scrollToActive()" class="border rounded px-2 py-1">
                                <template x-for="(d, idx) in details" :key="idx">
                                    <option :value="idx" x-text="'Line ' + (idx + 1)"></option>
                                </template>
                            </select>
                        </div>
                    </div>
                    <div class="mb-4 flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <button type="button" @click="prev()" :disabled="activeIndex === 0" class="px-3 py-1 rounded border hover:bg-gray-100 disabled:opacity-50">‹ Sebelumnya</button>
                            <button type="button" @click="next()" :disabled="activeIndex === details.length - 1" class="px-3 py-1 rounded border hover:bg-gray-100 disabled:opacity-50">Berikutnya ›</button>
                            <div class="ml-4 text-sm text-gray-600">
                                <span x-text="'Line ' + (activeIndex+1) + ' of ' + details.length"></span>
                            </div>
                        </div>
                        <button type="button" @click="removeLine(activeIndex)"
                            x-show="details.length > 0" :hidden="!isEditable" class="bg-red-600 text-white px-3 py-1 rounded hover:bg-red-700">
                            <i class="fa-solid fa-trash"></i> Hapus Line Ini
                        </button>
                    </div>
                    <template x-for="(d, idx) in details" :key="d.key">
                        <div x-show="activeIndex === idx" x-transition class="mb-4 bg-gray-50 p-4 rounded border">
                            <div class="grid grid-cols-1 gap-4">
                                <div> <label class="block font-semibold mb-1">Nomor *</label> <input type="text" :name="'details['+idx+'][nomor]'" x-model="d.nomor" class="w-full border rounded px-3 py-2" :readonly="!isEditable"> </div>
                                <div> <label class="block font-semibold mb-1">Reason *</label> <input type="text" :name="'details['+idx+'][reason]'" x-model="d.reason" class="w-full border rounded px-3 py-2" :readonly="!isEditable"> </div>
                                <div class="grid md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block font-semibold mb-1">Sebelum *</label>
                                        <textarea :name="'details['+idx+'][desc_before]'" x-model="d.desc_before" rows="6" class="w-full border rounded px-3 py-2" :readonly="!isEditable"></textarea>
                                    </div>
                                    <div>
                                        <label class="block font-semibold mb-1">Sesudah *</label>
                                        <textarea :name="'details['+idx+'][desc_after]'" x-model="d.desc_after" rows="6" class="w-full border rounded px-3 py-2" :readonly="!isEditable"></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </template>
                    <div x-show="details.length === 0" class="text-center py-8 text-gray-500"> Belum ada Line. Klik <button type="button" @click="addLine()" class="text-blue-600 underline">+ Tambah Line</button> untuk memulai. </div>
                </div>
                <!-- Tombol aksi -->
                <div id="actionButtons" class="flex justify-end gap-3 mt-4">
                    <button type="submit" name="action" value="draft" :hidden="!isEditable"
                        class="bg-gray-600 text-white px-4 py-2 rounded hover:bg-gray-700">
                        <i class="fa-solid fa-save"></i> Simpan Draft
                    </button>
                    <button type="submit" name="action" value="submit" :hidden="!isEditable"
                        class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                        <i class="fa-solid fa-paper-plane"></i> Ajukan
                    </button>
                </div>
            </form>
            <!-- Tab Attachment -->
            <div id="tab-attachment" class="tab-content hidden">
                <div class="mb-4">
                    <label class="block font-semibold mb-2">Lampiran</label>
                    @if($ticket->attachments->count() > 0)
                    <ul class="list-disc pl-5">
                        @foreach($ticket->attachments as $file)
                        <li class="p-1">
                            <a href="{{ asset('storage/' . $file->file_path) }}"
                                target="_blank"
                                class="d-block text-primary text-xs fw-semibold text-decoration-none">
                                <i class="fa-solid fa-file me-1 text-secondary"></i>
                                {{ $file->file_name }}
                            </a>
                            @if(in_array($ticket->status, ['Draft', 'Rejected']))
                            <form action="{{ route('tickets.attachment.delete', [$ticket->id, $file->id]) }}"
                                method="POST" class="delete-attachment-form">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                    class="btn btn-sm btn-outline-danger delete-btn text-xs">
                                    <i class="fa-solid fa-trash me-1"></i> Hapus
                                </button>
                            </form>
                            @endif
                        </li>
                        @endforeach
                    </ul>
                    @else
                    <p class="text-gray-500 text-sm">Belum ada lampiran.</p>
                    @endif

                    @if ($ticket->requestor_id == auth()->user()->id)
                    <form id="uploadAttachmentForm"
                        action="{{ route('tickets.attachment.upload', $ticket->id) }}"
                        method="POST"
                        enctype="multipart/form-data"
                        class="mt-4 border-t pt-4">
                        @csrf
                        <div>
                            <label class="block text-sm font-semibold mb-2">Upload Lampiran Baru</label>
                            <input type="file" name="attachments[]" multiple class="w-full border rounded px-3 py-2">
                        </div>
                        <button type="submit"
                            class="bg-blue-600 text-white text-sm mt-3 px-4 py-2 rounded hover:bg-blue-700 transition flex items-center gap-2">
                            <i class="fa-solid fa-upload"></i> Upload Lampiran
                        </button>
                    </form>
                    @endif
                </div>
            </div>
            <!-- Tab Log -->
            <div id="tab-log" class="tab-content hidden">
                <div class="mb-6">
                    <h3 class="text-lg font-semibold mb-3 flex items-center gap-2"> <i class="fa-solid fa-clock text-indigo-500"></i> Timeline Tiket </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4"> <!-- Created -->
                        <div class="bg-gray-50 border rounded-xl p-4 text-center shadow-sm">
                            <p class="text-sm text-gray-500">Tanggal Dibuat</p>
                            <p class="font-semibold text-gray-800"> {{ $ticket->created_at ? $ticket->created_at->format('d M Y H:i') : '-' }} </p>
                        </div> <!-- Approved -->
                        <div class="bg-gray-50 border rounded-xl p-4 text-center shadow-sm">
                            <p class="text-sm text-gray-500">Tanggal Disetujui</p>
                            <p class="font-semibold text-gray-800"> @if($ticket->approvedTicket && $ticket->approvedTicket->approved_at) {{ \Carbon\Carbon::parse($ticket->approvedTicket->approved_at)->format('d M Y H:i') }} @else - @endif </p>
                        </div> <!-- In Progress -->
                        <div class="bg-gray-50 border rounded-xl p-4 text-center shadow-sm">
                            <p class="text-sm text-gray-500">Tanggal Dikerjakan</p>
                            <p class="font-semibold text-gray-800"> {{ $ticket->finish_date ? $ticket->finish_date->format('d M Y H:i') : '-' }} </p>
                        </div> <!-- Closed -->
                        <div class="bg-gray-50 border rounded-xl p-4 text-center shadow-sm">
                            <p class="text-sm text-gray-500">Tanggal Ditutup</p>
                            <p class="font-semibold text-gray-800"> {{ $ticket->closed_date ? $ticket->closed_date->format('d M Y H:i') : '-' }} </p>
                        </div>
                    </div>
                </div>
                <hr class="my-6 border-gray-300">
                <div class="mb-4">
                    <h3 class="text-lg font-semibold mb-3 flex items-center gap-2"> <i class="fa-solid fa-list text-indigo-500"></i> Log Aktivitas </h3>
                    <div class="table-responsive overflow-x-auto border rounded-xl shadow-sm p-2">
                        <table id="LogTable" class="min-w-full text-sm text-left">
                            <thead class="bg-indigo-50 text-gray-700">
                                <tr>
                                    <th class="px-2 py-1 text-center">Tanggal</th>
                                    <th class="px-2 py-1 text-center">Aksi</th>
                                    <th class="px-2 py-1 text-center">User</th>
                                    <th class="px-2 py-1 text-left">Keterangan</th>
                                </tr>
                            </thead>
                            <tbody class=""> @forelse ($ticket->logs as $log)
                                <tr>
                                    <td class="px-2 py-1 text-gray-600"> {{ $log->created_at->format('d M Y H:i') }} </td>
                                    <td class="px-2 py-1 text-gray-700 font-semibold"> {{ ucfirst($log->action) }} </td>
                                    <td class="px-2 py-1 text-gray-700"> {{ $log->user->name ?? '-' }} </td>
                                    <td class="px-2 py-1 text-gray-600"> {{ $log->remark ?? '-' }} </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center py-4 text-gray-500">Belum ada log aktivitas.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <a href="{{ route('pdf.generate', $ticket->slug ) }}"
                class="btn btn-danger"
                target="_blank">
                <i class="fa-solid fa-file-pdf text-white-500 hover:text-red-400 text-lg"></i>
            </a>
        </div>
    </div>

    <!-- JQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/2.3.4/js/dataTables.js"></script>
    <script src="https://cdn.datatables.net/2.3.4/js/dataTables.bootstrap5.js"></script>

    <script>
        $(document).on('submit', 'form.delete-attachment-form', function(e) {
            e.preventDefault();
            const form = this;

            Swal.fire({
                title: 'Yakin hapus?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, hapus'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit(); // lanjut submit setelah konfirmasi
                }
            });
        });
        $(document).ready(function() {
            // dropdown submenu dinamis
            $('#menu_id').on('change', function() {
                var menuId = $(this).val();
                $('#sub_menu_id').html('<option value="">-- Pilih Sub Menu --</option>');
                if (menuId) {
                    $.ajax({
                        url: '/get-submenus/' + menuId,
                        type: 'GET',
                        dataType: 'json',
                        success: function(data) {
                            if (data.length > 0) {
                                $.each(data, function(key, subMenu) {
                                    $('#sub_menu_id').append('<option value="' + subMenu.id + '">' + subMenu.name + '</option>');
                                });
                            }
                        }
                    });
                }
            });
        });
        document.addEventListener('DOMContentLoaded', function() {
            const uploadBtn = document.getElementById('btnUploadAttachment');
            const fileInput = document.getElementById('attachmentFiles');
            const attachmentList = document.getElementById('attachmentList');
            // prettier-ignore
            const ticketId = Number("{{ $ticket->id }}"); //setiap refresh/save harus dirapikan lagi karena sensitive

            uploadBtn.addEventListener('click', function(e) {
                e.preventDefault();

                if (!fileInput.files.length) {
                    alert('Silakan pilih file terlebih dahulu.');
                    return;
                }

                const formData = new FormData();
                for (let i = 0; i < fileInput.files.length; i++) {
                    formData.append('attachments[]', fileInput.files[i]);
                }

                fetch(`/tickets/attachment/${ticketId}/upload`, {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        }
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            alert('File berhasil diupload!');
                            attachmentList.innerHTML = '';
                            data.attachments.forEach(file => {
                                const li = document.createElement('li');
                                li.classList.add(`attachment-row-${file.id}`);
                                li.innerHTML = `
                        <a href="${file.url}" target="_blank" class="text-blue-600 underline">${file.file_name}</a>
                        <button type="button" class="btn-delete-attachment text-red-600 ml-2" data-id="${file.id}">Hapus</button>
                    `;
                                attachmentList.appendChild(li);
                            });
                            fileInput.value = '';
                        } else {
                            alert('Gagal upload file.');
                        }
                    })
                    .catch(err => {
                        console.error(err);
                        alert('Terjadi kesalahan saat upload file.');
                    });
            });

            attachmentList.addEventListener('click', function(e) {
                if (!e.target.classList.contains('btn-delete-attachment')) return;

                const attachmentId = e.target.dataset.id;

                Swal.fire({
                    title: 'Yakin hapus file ini?',
                    text: "Tindakan ini tidak bisa dikembalikan!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Hapus',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {

                        fetch(`/tickets/${ticketId}/attachment/${attachmentId}`, {
                                method: 'DELETE',
                                headers: {
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                    'Accept': 'application/json'
                                },
                                credentials: 'same-origin'
                            })
                            .then(r => r.json())
                            .then(data => {
                                if (data.success) {
                                    const row = document.querySelector(`.attachment-row-${attachmentId}`);
                                    if (row) row.remove();

                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Berhasil',
                                        text: 'File telah dihapus'
                                    });
                                } else {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Gagal',
                                        text: 'File tidak dapat dihapus'
                                    });
                                }
                            });
                    }
                });
            });
        });
    </script>
    <!-- CSS Tabs -->
    <style>
        .tab-btn {
            padding: 8px 16px;
            font-weight: 600;
            border: none;
            background-color: #f3f4f6;
            cursor: pointer;
        }

        .tab-btn.active-tab {
            background-color: #2563eb;
            color: white;
            border-bottom: 2px solid #2563eb;
        }

        .tab-content {
            display: none;
        }

        .tab-content.block {
            display: block;
        }
    </style>

    <script>
        $(document).ready(function() {
            $('#LogTable').DataTable({
                pageLength: 10,
                lengthMenu: [5, 10, 25, 50, 100],
                ordering: false,
                searching: false,
                language: {
                    lengthMenu: "Tampilkan _MENU_ data",
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
                    className: 'text-start align-middle'
                }]
            });
        });
        document.querySelectorAll('.tab-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                // Ganti tab
                document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active-tab'));
                document.querySelectorAll('.tab-content').forEach(c => c.classList.remove('block'));
                btn.classList.add('active-tab');
                const activeTab = btn.dataset.tab;
                document.getElementById(activeTab).classList.add('block');

                // Sembunyikan tombol aksi jika tab = attachment/log
                const actionButtons = document.getElementById('actionButtons');
                if (activeTab === 'tab-attachment' || activeTab === 'tab-log') {
                    actionButtons.style.display = 'none';
                } else {
                    actionButtons.style.display = 'flex';
                }
            });
        });
    </script>

    <script>
        function ticketForm(existingDetails = [], status = 'draft') {
            console.log('Creating new Alpine instance ticketForm');
            return {
                details: (existingDetails || [])
                    .filter((d, i, arr) =>
                        i === arr.findIndex(x => x.ticket_head_id === d.ticket_head_id && x.ticket_line === d.ticket_line)
                    )
                    .map(d => ({
                        id: d.id ?? null,
                        ticket_head_id: d.ticket_head_id,
                        ticket_line: d.ticket_line,
                        nomor: d.nomor ?? '',
                        reason: d.reason ?? '',
                        desc_before: d.desc_before ?? '',
                        desc_after: d.desc_after ?? '',
                        key: `${d.ticket_head_id}_${d.ticket_line}`
                    })),

                activeIndex: 0,
                status: (status || '').toLowerCase(),

                get isEditable() {
                    return ['draft', 'rejected'].includes(this.status);
                },

                addLine() {
                    if (!this.isEditable) return;
                    const nextLine = (this.details.length > 0 ?
                        Math.max(...this.details.map(d => d.ticket_line)) + 1 :
                        1);
                    this.details.push({
                        id: null,
                        ticket_head_id: this.details[0]?.ticket_head_id ?? null,
                        ticket_line: nextLine,
                        nomor: '',
                        reason: '',
                        desc_before: '',
                        desc_after: '',
                        key: `new_${nextLine}`
                    });
                    this.activeIndex = this.details.length - 1;
                    this.$nextTick(() => this.scrollToActive());
                },

                removeLine(index) {
                    if (!this.isEditable) return;
                    if (!confirm('Hapus line ' + (index + 1) + ' ?')) return;
                    this.details.splice(index, 1);
                    this.activeIndex = Math.max(0, this.activeIndex - 1);
                },

                prev() {
                    if (this.activeIndex > 0) this.activeIndex--;
                },

                next() {
                    if (this.activeIndex < this.details.length - 1) this.activeIndex++;
                },

                scrollToActive() {
                    this.$nextTick(() => {
                        const el = document.querySelector('[name="details[' + this.activeIndex + '][nomor]"]');
                        if (el) el.focus();
                    });
                },
                init() {
                    if (this.initialized) return; // 🛑 cegah re-run
                    this.initialized = true;

                    console.log('✅ ticketForm initialized once with', this.details.length, 'details');
                    const seen = new Set();
                    this.details.forEach(d => {
                        const key = `${d.ticket_head_id}_${d.ticket_line}`;
                        if (seen.has(key)) console.warn('Duplicate line detected:', key);
                        seen.add(key);
                    });
                }
            };
        }
    </script>
</x-app-layout>