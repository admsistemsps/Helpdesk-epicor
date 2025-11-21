<x-app-layout>
    <main class="flex-1">
        <x-breadcrumb />

        <div class="bg-white p-6 space-y-6 rounded-lg shadow">
            <div class="mb-6">
                <h2 class="text-2xl font-semibold text-gray-800">Edit Menu: {{ $menu->name }}</h2>
            </div>

            <!-- Alert Messages -->
            @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                {{ session('success') }}
            </div>
            @endif

            @if(session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                {{ session('error') }}
            </div>
            @endif

            @if($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                <ul class="list-disc list-inside">
                    @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <!-- NAV TABS -->
            <ul class="nav nav-tabs border-b" id="menuTabs" role="tablist">
                <li class="nav-item">
                    <button class="nav-link active px-4 py-2" id="tab-menu" data-bs-toggle="tab" data-bs-target="#menuTab"
                        type="button" role="tab">
                        <i class="fa-solid fa-list"></i> Menu
                    </button>
                </li>
                <li class="nav-item">
                    <button class="nav-link px-4 py-2" id="tab-submenu" data-bs-toggle="tab" data-bs-target="#submenuTab"
                        type="button" role="tab">
                        <i class="fa-solid fa-bars-staggered"></i> Sub Menu
                    </button>
                </li>
            </ul>

            <!-- TAB CONTENT -->
            <div class="tab-content mt-4" id="menuTabsContent">
                <!-- TAB 1: Edit Menu -->
                <div class="tab-pane fade show active" id="menuTab" role="tabpanel">
                    <form action="{{ route('menus.update', $menu->id) }}" method="POST" autocomplete="off">
                        @csrf
                        @method('PUT')

                        <div class="mb-4">
                            <label for="name" class="block text-sm font-bold text-gray-700 mb-1">
                                Nama Menu <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="name" id="name" value="{{ old('name', $menu->name) }}" required
                                class="border rounded w-full py-2 px-3 focus:outline-none focus:ring-2 focus:ring-indigo-300 @error('name') border-red-500 @enderror">
                            @error('name')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="description" class="block text-sm font-bold text-gray-700 mb-1">Deskripsi</label>
                            <textarea name="description" id="description" rows="3"
                                class="border rounded w-full py-2 px-3 focus:outline-none focus:ring-2 focus:ring-indigo-300 @error('description') border-red-500 @enderror">{{ old('description', $menu->description) }}</textarea>
                            @error('description')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="division_ids" class="block text-sm font-bold text-gray-700 mb-2">
                                Untuk Divisi <span class="text-red-500">*</span>
                            </label>
                            <select name="division_ids[]" id="division_ids" multiple class="w-full">
                                @foreach ($divisions as $division)
                                <option value="{{ $division->id }}"
                                    {{ in_array($division->id, old('division_ids', $menu->divisions->pluck('id')->toArray())) ? 'selected' : '' }}>
                                    {{ $division->name }}
                                </option>
                                @endforeach
                            </select>
                            <p class="text-xs text-gray-500 mt-2">
                                <i class="fa-solid fa-info-circle"></i> Pilih satu atau lebih divisi
                            </p>
                            @error('division_ids')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="department_ids" class="block text-sm font-bold text-gray-700 mb-2">
                                Untuk Departemen (Opsional)
                            </label>
                            <select name="department_ids[]" id="department_ids" multiple class="w-full">
                                @foreach ($departments as $department)
                                <option value="{{ $department->id }}"
                                    {{ in_array($department->id, old('department_ids', $menu->departments->pluck('id')->toArray())) ? 'selected' : '' }}>
                                    {{ $department->name }}
                                </option>
                                @endforeach
                            </select>
                            <p class="text-xs text-gray-500 mt-2">
                                <i class="fa-solid fa-info-circle"></i> Kosongkan jika tidak terbatas departemen
                            </p>
                            @error('department_ids')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex gap-3">
                            <button type="submit" class="bg-indigo-600 text-white px-6 py-2 rounded hover:bg-indigo-700 transition">
                                <i class="fa-solid fa-save"></i> Simpan Perubahan
                            </button>
                            <a href="{{ route('menus.index') }}" class="bg-gray-500 text-white px-6 py-2 rounded hover:bg-gray-600 transition">
                                <i class="fa-solid fa-arrow-left"></i> Kembali
                            </a>
                        </div>
                    </form>
                </div>

                <!-- TAB 2: Sub Menu -->
                <div class="tab-pane fade" id="submenuTab" role="tabpanel">
                    <!-- Header dengan Tombol Tambah -->
                    <div class="flex justify-between items-center mb-4">
                        <div>
                            <h5 class="font-semibold text-lg">Daftar Sub Menu</h5>
                            <p class="text-sm text-gray-600">Total: {{ $menu->subMenus->count() }} sub menu</p>
                        </div>
                        <button type="button" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 transition"
                            data-bs-toggle="modal" data-bs-target="#addSubMenuModal">
                            <i class="fa-solid fa-plus"></i> Tambah Sub Menu
                        </button>
                    </div>

                    <!-- Tabel Sub Menu -->
                    <div class="overflow-x-auto">
                        <table id="SubMenusTable" class="table table-bordered table-striped w-full">
                            <thead class="bg-gray-200">
                                <tr>
                                    <th class="px-4 py-2 w-12">#</th>
                                    <th class="px-4 py-2">Nama Sub Menu</th>
                                    <th class="px-4 py-2">Deskripsi</th>
                                    <th class="px-4 py-2">Placeholder</th>
                                    <th class="px-4 py-2 text-center w-40">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($menu->subMenus as $index => $submenu)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-2">{{ $index + 1 }}</td>
                                    <td class="px-4 py-2 font-medium">{{ $submenu->name }}</td>
                                    <td class="px-4 py-2">{{ $submenu->description ?: '-' }}</td>
                                    <td class="px-4 py-2 text-gray-600 text-sm">{{ $submenu->placeholder ?: '-' }}</td>
                                    <td class="px-4 py-2 text-center">
                                        <div class="flex gap-2 justify-center">
                                            <!-- Tombol Edit -->
                                            <button type="button"
                                                class="btn btn-sm btn-warning text-white editSubMenuBtn"
                                                data-id="{{ $submenu->id }}"
                                                data-name="{{ $submenu->name }}"
                                                data-description="{{ $submenu->description }}"
                                                data-placeholder="{{ $submenu->placeholder }}">
                                                <i class="fa-solid fa-pen"></i>
                                            </button>

                                            <!-- Tombol Hapus -->
                                            <form action="{{ route('sub-menus.destroy', $submenu->id) }}" method="POST"
                                                class="d-inline delete-form">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger">
                                                    <i class="fa-solid fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center text-gray-500 py-8">
                                        <i class="fa-solid fa-inbox text-4xl mb-2 text-gray-400"></i>
                                        <p>Belum ada sub menu. Klik tombol "Tambah Sub Menu" untuk menambahkan.</p>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Tambah Sub Menu -->
        <div class="modal fade" id="addSubMenuModal" tabindex="-1" aria-labelledby="addSubMenuModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <form action="{{ route('sub-menus.store') }}" method="POST" autocomplete="off" class="modal-content">
                    @csrf
                    <input type="hidden" name="menu_id" value="{{ $menu->id }}">

                    <div class="modal-header bg-green-600 text-white">
                        <h5 class="modal-title" id="addSubMenuModalLabel">
                            <i class="fa-solid fa-plus-circle"></i> Tambah Sub Menu
                        </h5>
                        <button type="button" class="btn-close bg-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="subMenuName" class="block text-sm font-semibold mb-1">
                                Nama Sub Menu <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="name" id="subMenuName"
                                class="border rounded w-full py-2 px-3 focus:outline-none focus:ring-2 focus:ring-green-300"
                                placeholder="Masukkan nama sub menu" required>
                        </div>
                        <div class="mb-3">
                            <label for="subMenuDesc" class="block text-sm font-semibold mb-1">Deskripsi</label>
                            <textarea name="description" id="subMenuDesc" rows="3"
                                class="border rounded w-full py-2 px-3 focus:outline-none focus:ring-2 focus:ring-green-300"
                                placeholder="Masukkan deskripsi sub menu (opsional)"></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="subMenuPlaceholder" class="block text-sm font-semibold mb-1">Placeholder</label>
                            <input type="text" name="placeholder" id="subMenuPlaceholder"
                                class="border rounded w-full py-2 px-3 focus:outline-none focus:ring-2 focus:ring-green-300"
                                placeholder="Masukkan placeholder (opsional)">
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="fa-solid fa-times"></i> Batal
                        </button>
                        <button type="submit" class="btn btn-success">
                            <i class="fa-solid fa-save"></i> Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Modal Edit Sub Menu -->
        <div class="modal fade" id="editSubMenuModal" tabindex="-1" aria-labelledby="editSubMenuModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <form id="editSubMenuForm" method="POST" class="modal-content">
                    @csrf
                    @method('PUT')

                    <div class="modal-header bg-yellow-500 text-white">
                        <h5 class="modal-title" id="editSubMenuModalLabel">
                            <i class="fa-solid fa-edit"></i> Edit Sub Menu
                        </h5>
                        <button type="button" class="btn-close bg-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <div class="modal-body">
                        <input type="hidden" name="id" id="editSubMenuId">

                        <div class="mb-3">
                            <label for="editSubMenuName" class="block text-sm font-semibold mb-1">
                                Nama Sub Menu <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="name" id="editSubMenuName"
                                class="border rounded w-full py-2 px-3 focus:outline-none focus:ring-2 focus:ring-yellow-300"
                                required>
                        </div>
                        <div class="mb-3">
                            <label for="editSubMenuDesc" class="block text-sm font-semibold mb-1">Deskripsi</label>
                            <textarea name="description" id="editSubMenuDesc" rows="3"
                                class="border rounded w-full py-2 px-3 focus:outline-none focus:ring-2 focus:ring-yellow-300"></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="editSubMenuPlaceholder" class="block text-sm font-semibold mb-1">Placeholder</label>
                            <input type="text" name="placeholder" id="editSubMenuPlaceholder"
                                class="border rounded w-full py-2 px-3 focus:outline-none focus:ring-2 focus:ring-yellow-300">
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="fa-solid fa-times"></i> Batal
                        </button>
                        <button type="submit" class="btn btn-warning text-white">
                            <i class="fa-solid fa-save"></i> Update
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <script>
            document.addEventListener('DOMContentLoaded', () => {
                // Initialize Select2 dengan konfigurasi lengkap
                $('#division_ids').select2({
                    placeholder: 'Pilih divisi (bisa lebih dari satu)',
                    width: '100%',
                    allowClear: true,
                    closeOnSelect: false,
                    theme: 'bootstrap-5', // Gunakan tema bootstrap-5 jika tersedia
                    language: {
                        noResults: function() {
                            return "Tidak ada hasil";
                        },
                        searching: function() {
                            return "Mencari...";
                        }
                    }
                });

                $('#department_ids').select2({
                    placeholder: 'Pilih departemen (opsional)',
                    width: '100%',
                    allowClear: true,
                    closeOnSelect: false,
                    theme: 'bootstrap-5',
                    language: {
                        noResults: function() {
                            return "Tidak ada hasil";
                        },
                        searching: function() {
                            return "Mencari...";
                        }
                    }
                });

                // Initialize DataTable (jika menggunakan DataTables)
                if ($.fn.DataTable) {
                    $('#SubMenusTable').DataTable({
                        language: {
                            url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/id.json'
                        },
                        order: [
                            [0, 'asc']
                        ],
                        pageLength: 10,
                        responsive: true
                    });
                }

                // SweetAlert untuk konfirmasi hapus
                document.addEventListener('submit', function(e) {
                    if (e.target.classList.contains('delete-form')) {
                        e.preventDefault();
                        Swal.fire({
                            title: 'Hapus Sub Menu?',
                            text: 'Data ini tidak dapat dikembalikan setelah dihapus!',
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#dc2626',
                            cancelButtonColor: '#6b7280',
                            confirmButtonText: 'Ya, Hapus!',
                            cancelButtonText: 'Batal'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                e.target.submit();
                            }
                        });
                    }
                });

                // Handler untuk tombol Edit Sub Menu
                document.querySelectorAll('.editSubMenuBtn').forEach(btn => {
                    btn.addEventListener('click', () => {
                        const id = btn.dataset.id;
                        const name = btn.dataset.name;
                        const description = btn.dataset.description;
                        const placeholder = btn.dataset.placeholder;

                        // Isi data ke form modal
                        document.getElementById('editSubMenuId').value = id;
                        document.getElementById('editSubMenuName').value = name;
                        document.getElementById('editSubMenuDesc').value = description || '';
                        document.getElementById('editSubMenuPlaceholder').value = placeholder || '';

                        // Update action form
                        const form = document.getElementById('editSubMenuForm');
                        form.action = `/sub-menus/${id}`;

                        // Tampilkan modal
                        const modal = new bootstrap.Modal(document.getElementById('editSubMenuModal'));
                        modal.show();
                    });
                });
            });
        </script>
    </main>
</x-app-layout>