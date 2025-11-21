<x-app-layout>
    @push('scripts')
    <script>
        window.ticketDetails = @json($details ?? []);
        window.ticketStatus = "{{ strtolower($ticket->status ?? 'draft') }}";
        let currentPlaceholder = '';
    </script>
    @endpush
    <div x-data="ticketForm" x-init="init()">
        <div class="max-w-auto mx-auto bg-white shadow-md rounded-lg p-6">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-bold mb-4">Create Ticket</h2>
                <div class="flex items-center px-4 py-2 rounded-md space-x-2 ">
                    <span class="px-2.5 py-1 rounded text-xs font-semibold">
                        Status Ticket
                    </span>
                </div>
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

            <form id="ticketForm" action="{{ route('tickets.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="border-b mb-4 flex">
                    <button type="button" class="tab-btn active-tab" data-tab="tab-form">Header</button>
                    <button type="button" class="tab-btn" data-tab="tab-detail">Detail</button>
                    <button type="button" class="tab-btn" data-tab="tab-attachment">Attachment</button>
                </div> <!-- Tab Header -->
                <div id="tab-form" class="tab-content block">
                    <div class="mb-4">
                        <label class="block font-semibold mb-2">Nomor FUHD</label>
                        <input type="text" name="nomor_fuhd" readonly class="w-full border rounded px-3 py-2 bg-gray-200 border-gray-300">
                    </div>
                    <div class="mb-3">
                        <label for="menu_id" class="form-label">Menu</label>
                        <select id="menu_id" name="menu_id" class="form-select" required>
                            <option value="">-- Pilih Menu --</option>
                            @foreach($menus as $menu)
                            <option value="{{ $menu->id }}">{{ $menu->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="sub_menu_id" class="form-label">Sub Menu</label>
                        <select id="sub_menu_id" name="sub_menu_id" class="form-select" required>
                            <option value="">-- Pilih Sub Menu --</option>
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
                            <!-- <span class="text-sm text-gray-600">Line</span>  -->
                        </div>
                    </div>
                    <div class="mb-4 flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <button type="button" @click="prev()" :disabled="activeIndex === 0" class="px-3 py-1 rounded border hover:bg-gray-100 disabled:opacity-50">‹ Sebelumnya</button>
                            <select x-model.number="activeIndex" @change="scrollToActive()" class="border rounded px-2 py-1">
                                <template x-for="(d, idx) in details" :key="idx">
                                    <option :value="idx" x-text="'Line ' + (idx + 1)"></option>
                                </template>
                            </select>
                            <button type="button" @click="next()" :disabled="activeIndex === details.length - 1" class="px-3 py-1 rounded border hover:bg-gray-100 disabled:opacity-50">Berikutnya ›</button>
                            <div class="ml-4 text-sm text-gray-600">
                                <span
                                    x-text="details.length > 0 
                                    ? 'Line ' + (activeIndex + 1) + ' of ' + details.length 
                                    : 'Line 0 of 0'">
                                </span>
                            </div>
                        </div>
                        <button type="button" @click="removeLine(activeIndex)" x-show="details.length > 0" :hidden="!isEditable" class="bg-red-600 text-white px-3 py-1 rounded hover:bg-red-700"> Hapus Line Ini </button>
                    </div>
                    <template x-for="(d, idx) in details" :key="d.key">
                        <div x-show="activeIndex === idx" x-transition class="mb-4 bg-gray-50 p-4 rounded border">
                            <div class="grid grid-cols-1 gap-4">
                                <div>
                                    <label class="block font-semibold mb-1">Nomor *</label>
                                    <input type="text"
                                        :name="'details['+idx+'][nomor]'"
                                        x-model="d.nomor"
                                        :placeholder="nomorPlaceholder"
                                        class="w-full border rounded px-3 py-2"
                                        :readonly="!isEditable">
                                </div>

                                <div>
                                    <label class="block font-semibold mb-1">Reason *</label>
                                    <input type="text"
                                        :name="'details['+idx+'][reason]'"
                                        x-model="d.reason"
                                        placeholder="Alasan perubahan"
                                        class="w-full border rounded px-3 py-2"
                                        :readonly="!isEditable">
                                </div>

                                <div class="grid md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block font-semibold mb-1">Sebelum *</label>
                                        <textarea
                                            :name="'details['+idx+'][desc_before]'"
                                            x-model="d.desc_before"
                                            rows="6"
                                            placeholder="Deskripsi sebelum perubahan"
                                            class="w-full border rounded px-3 py-2"
                                            :readonly="!isEditable"></textarea>
                                    </div>

                                    <div>
                                        <label class="block font-semibold mb-1">Sesudah *</label>
                                        <textarea
                                            :name="'details['+idx+'][desc_after]'"
                                            x-model="d.desc_after"
                                            rows="6"
                                            placeholder="Deskripsi sesudah perubahan"
                                            class="w-full border rounded px-3 py-2"
                                            :readonly="!isEditable"></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </template>
                    <div x-show="details.length === 0" class="text-center py-8 text-gray-500"> Belum ada Line. Klik <button type="button" @click="addLine()" class="text-blue-600 underline">+ Tambah Line</button> untuk memulai. </div>
                </div>

                <div id="tab-attachment" class="tab-content hidden">
                    <div class="mb-4"> <label class="block font-semibold mb-2">Lampiran</label> <input type="file" name="attachments[]" multiple class="border rounded px-3 py-2 w-full focus:outline-none focus:ring focus:border-blue-300">
                        <p class="text-xs text-gray-500 mt-1">Format: PDF, JPG, PNG, DOCX, dll. Max 5MB per file.</p>
                    </div>
                </div>
                <!-- Tombol aksi -->
                <div class="flex justify-end gap-3 mt-4"> <button type="submit" name="action" value="draft" :hidden="!isEditable" class="bg-gray-600 text-white px-4 py-2 rounded hover:bg-gray-700"> <i class="fa-solid fa-save"></i> Simpan Draft </button> <button type="submit" name="action" value="submit" :hidden="!isEditable" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700"> <i class="fa-solid fa-paper-plane"></i> Ajukan </button> </div>
            </form>
        </div>
    </div>

    <!-- JQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script>
        $(document).ready(function() {
            // dropdown submenu dinamis
            $('#menu_id').on('change', function() {
                var menuId = $(this).val();
                $('#sub_menu_id').html('<option value="">-- Pilih Sub Menu --</option>');

                // Reset placeholder when menu changes
                currentPlaceholder = '';
                $('input[name*="[nomor]"]').attr('placeholder', 'Masukkan nomor');

                if (menuId) {
                    $.ajax({
                        url: '/get-submenus/' + menuId,
                        type: 'GET',
                        dataType: 'json',
                        success: function(data) {
                            if (data.length > 0) {
                                $.each(data, function(key, subMenu) {
                                    $('#sub_menu_id').append(
                                        '<option value="' + subMenu.id + '" data-placeholder="' +
                                        (subMenu.placeholder || '') + '">' +
                                        subMenu.name + '</option>'
                                    );
                                });
                            }
                        }
                    });
                }
            });

            // Update placeholder when sub-menu changes
            $('#sub_menu_id').on('change', function() {
                let selectedOption = $(this).find('option:selected');
                let placeholder = selectedOption.data('placeholder') || '';

                // Store globally
                currentPlaceholder = placeholder;

                // Update Alpine.js component placeholder
                const alpineComponent = Alpine.$data(document.querySelector('[x-data="ticketForm"]'));
                if (alpineComponent) {
                    if (placeholder) {
                        alpineComponent.nomorPlaceholder = 'Masukkan nomor ' + placeholder;
                    } else {
                        alpineComponent.nomorPlaceholder = 'Masukkan nomor';
                    }
                }

                console.log('Placeholder updated:', placeholder);
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

    <!-- JS Tab Switch -->
    <script>
        document.querySelectorAll('.tab-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active-tab'));
                document.querySelectorAll('.tab-content').forEach(c => c.classList.remove('block'));
                btn.classList.add('active-tab');
                document.getElementById(btn.dataset.tab).classList.add('block');
            });
        });
    </script>

    <script>
        function ticketForm() {
            return {
                details: [],
                activeIndex: 0,
                initialized: false,
                isEditable: window.ticketStatus === 'draft' || !window.ticketStatus,
                nomorPlaceholder: 'Masukkan nomor',

                init() {
                    if (this.initialized) return;
                    this.initialized = true;

                    // Load existing details if any
                    if (window.ticketDetails && window.ticketDetails.length > 0) {
                        this.details = window.ticketDetails.map((d, i) => ({
                            id: d.id || null,
                            ticket_line: d.ticket_line || (i + 1),
                            nomor: d.nomor || '',
                            reason: d.reason || '',
                            desc_before: d.desc_before || '',
                            desc_after: d.desc_after || '',
                            key: Date.now() + i
                        }));
                    } else {
                        // Start with one empty line
                        this.addLine();
                    }

                    console.log('✅ ticketForm initialized', this.details.length, 'lines');
                },

                addLine() {
                    const newLine = {
                        id: null,
                        ticket_line: this.details.length + 1,
                        nomor: '',
                        reason: '',
                        desc_before: '',
                        desc_after: '',
                        key: Date.now()
                    };

                    this.details.push(newLine);
                    this.activeIndex = this.details.length - 1;

                    console.log('➕ Added line', this.details.length);
                },

                removeLine(index) {
                    if (this.details.length === 1) {
                        alert('Minimal harus ada 1 line');
                        return;
                    }

                    if (confirm('Yakin hapus line ini?')) {
                        this.details.splice(index, 1);

                        // Reorder ticket_line numbers
                        this.details.forEach((d, i) => {
                            d.ticket_line = i + 1;
                        });

                        if (this.activeIndex >= this.details.length) {
                            this.activeIndex = this.details.length - 1;
                        }

                        console.log('🗑️ Removed line', index);
                    }
                },

                prev() {
                    if (this.activeIndex > 0) {
                        this.activeIndex--;
                    }
                },

                next() {
                    if (this.activeIndex < this.details.length - 1) {
                        this.activeIndex++;
                    }
                },

                scrollToActive() {
                    console.log('Active line:', this.activeIndex + 1);
                }
            };
        }
    </script>
</x-app-layout>