<x-app-layout>
    <main class="flex-1">
        <x-breadcrumb />

        <!-- Header -->
        <div class="mb-6">
            <h2 class="text-2xl font-semibold text-gray-800">Tambah Menu</h2>
        </div>

        <!-- Alert Error -->
        @if($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            <ul class="list-disc list-inside">
                @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <div class="bg-white rounded-lg shadow p-6">
            <form action="{{ route('menus.store') }}" method="POST" id="menuForm">
                @csrf

                <!-- Nama & Deskripsi -->
                <div class="grid md:grid-cols-2 gap-4 mb-6">
                    <div>
                        <label class="block font-bold text-sm text-gray-700 mb-2">
                            Nama Menu <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="name" value="{{ old('name') }}" required
                            class="border rounded w-full py-2 px-3 focus:outline-none focus:ring-2 focus:ring-purple-300 @error('name') border-red-500 @enderror"
                            placeholder="Masukkan nama menu">
                        @error('name')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block font-bold text-sm text-gray-700 mb-2">Deskripsi</label>
                        <input type="text" name="description" value="{{ old('description') }}"
                            class="border rounded w-full py-2 px-3 focus:outline-none focus:ring-2 focus:ring-purple-300 @error('description') border-red-500 @enderror"
                            placeholder="Masukkan deskripsi menu">
                        @error('description')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Multi Division -->
                <div class="mb-6">
                    <label class="block text-sm font-bold text-gray-700 mb-2">
                        Untuk Divisi <span class="text-red-500">*</span>
                    </label>
                    <select name="division_ids[]" id="divisionSelect" multiple class="w-full">
                        <option value="">Pilih Divisi</option>
                        @foreach ($divisions as $division)
                        <option value="{{ $division->id }}" 
                            {{ in_array($division->id, old('division_ids', [])) ? 'selected' : '' }}>
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

                <!-- Multi Department -->
                <div class="mb-6">
                    <label class="block text-sm font-bold text-gray-700 mb-2">
                        Untuk Departemen (Opsional)
                    </label>
                    <select name="department_ids[]" id="departmentSelect" multiple class="w-full">
                        <option value="">Pilih Departemen</option>
                        @foreach ($departments as $department)
                        <option value="{{ $department->id }}" 
                            {{ in_array($department->id, old('department_ids', [])) ? 'selected' : '' }}>
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

                <!-- Sub Menu -->
                <div x-data="subMenuHandler()" class="mb-6">
                    <div class="flex items-center justify-between mb-3">
                        <h3 class="font-semibold text-lg">Sub Menu</h3>
                        <span class="text-sm text-gray-600" x-text="subMenus.length + ' sub menu'"></span>
                    </div>

                    <!-- Tabel Sub Menu -->
                    <div class="overflow-x-auto mb-4">
                        <table class="w-full border border-gray-300 text-sm">
                            <thead class="bg-gray-100">
                                <tr>
                                    <th class="border px-3 py-2 text-left w-10">#</th>
                                    <th class="border px-3 py-2 text-left">Nama</th>
                                    <th class="border px-3 py-2 text-left">Deskripsi</th>
                                    <th class="border px-3 py-2 text-left">Placeholder</th>
                                    <th class="border px-3 py-2 text-center w-24">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <template x-if="subMenus.length === 0">
                                    <tr>
                                        <td colspan="5" class="border px-3 py-4 text-center text-gray-500">
                                            Belum ada sub menu. Tambahkan sub menu menggunakan form di bawah.
                                        </td>
                                    </tr>
                                </template>
                                <template x-for="(submenu, index) in subMenus" :key="index">
                                    <tr class="hover:bg-gray-50">
                                        <td class="border px-3 py-2" x-text="index + 1"></td>
                                        <td class="border px-3 py-2" x-text="submenu.name"></td>
                                        <td class="border px-3 py-2" x-text="submenu.description || '-'"></td>
                                        <td class="border px-3 py-2" x-text="submenu.placeholder || '-'"></td>
                                        <td class="border px-3 py-2 text-center">
                                            <button type="button" @click="removeSubMenu(index)"
                                                class="text-red-600 hover:text-red-800 hover:underline transition">
                                                <i class="fa-solid fa-trash"></i> Hapus
                                            </button>
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>

                    <!-- Form Tambah Sub Menu -->
                    <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                        <h4 class="font-semibold text-sm mb-3 text-gray-700">Tambah Sub Menu Baru</h4>
                        <div class="grid md:grid-cols-4 gap-3">
                            <div>
                                <input type="text" x-model="newSubMenuName" placeholder="Nama Sub Menu *"
                                    class="border rounded w-full px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-300"
                                    @keydown.enter.prevent="addSubMenu()">
                            </div>
                            <div>
                                <input type="text" x-model="newSubMenuDescription" placeholder="Deskripsi"
                                    class="border rounded w-full px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-300"
                                    @keydown.enter.prevent="addSubMenu()">
                            </div>
                            <div>
                                <input type="text" x-model="newSubMenuPlaceholder" placeholder="Placeholder"
                                    class="border rounded w-full px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-300"
                                    @keydown.enter.prevent="addSubMenu()">
                            </div>
                            <div>
                                <button type="button" @click="addSubMenu()"
                                    class="bg-green-600 text-white px-4 py-2 rounded w-full hover:bg-green-700 transition">
                                    <i class="fa-solid fa-plus"></i> Tambah
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Hidden inputs untuk submit -->
                    <template x-for="(submenu, index) in subMenus" :key="'hidden-' + index">
                        <div>
                            <input type="hidden" :name="'sub_menus['+index+'][name]'" :value="submenu.name">
                            <input type="hidden" :name="'sub_menus['+index+'][description]'" :value="submenu.description">
                            <input type="hidden" :name="'sub_menus['+index+'][placeholder]'" :value="submenu.placeholder">
                        </div>
                    </template>
                </div>

                <!-- Action Buttons -->
                <div class="flex gap-3">
                    <button type="submit"
                        class="bg-purple-600 text-white px-6 py-2 rounded-lg hover:bg-purple-700 transition">
                        <i class="fa-solid fa-save"></i> Simpan Menu
                    </button>
                    <a href="{{ route('menus.index') }}"
                        class="bg-gray-500 text-white px-6 py-2 rounded-lg hover:bg-gray-600 transition">
                        <i class="fa-solid fa-arrow-left"></i> Kembali
                    </a>
                </div>
            </form>
        </div>

        <!-- Alpine.js & Select2 Scripts -->
        <script>
            function subMenuHandler() {
                return {
                    subMenus: [],
                    newSubMenuName: '',
                    newSubMenuDescription: '',
                    newSubMenuPlaceholder: '',

                    addSubMenu() {
                        const name = this.newSubMenuName.trim();

                        if (name === '') {
                            Swal.fire({
                                icon: 'warning',
                                title: 'Nama Sub Menu Wajib Diisi',
                                text: 'Silakan isi nama sub menu terlebih dahulu.',
                            });
                            return;
                        }

                        this.subMenus.push({
                            name: name,
                            description: this.newSubMenuDescription.trim(),
                            placeholder: this.newSubMenuPlaceholder.trim()
                        });

                        // Reset form
                        this.newSubMenuName = '';
                        this.newSubMenuDescription = '';
                        this.newSubMenuPlaceholder = '';

                        // Show success message
                        Swal.fire({
                            icon: 'success',
                            title: 'Sub Menu Ditambahkan',
                            text: `"${name}" berhasil ditambahkan ke daftar.`,
                            timer: 1500,
                            showConfirmButton: false
                        });
                    },

                    removeSubMenu(index) {
                        const submenu = this.subMenus[index];

                        Swal.fire({
                            title: 'Hapus Sub Menu?',
                            text: `Apakah Anda yakin ingin menghapus "${submenu.name}"?`,
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#dc2626',
                            cancelButtonColor: '#6b7280',
                            confirmButtonText: 'Ya, Hapus',
                            cancelButtonText: 'Batal'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                this.subMenus.splice(index, 1);
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Dihapus',
                                    text: 'Sub menu berhasil dihapus.',
                                    timer: 1500,
                                    showConfirmButton: false
                                });
                            }
                        });
                    }
                };
            }

            $(document).ready(function() {
                // Initialize Select2 dengan konfigurasi lengkap (sama dengan edit)
                $('#divisionSelect').select2({
                    placeholder: 'Pilih divisi (bisa lebih dari satu)',
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

                $('#departmentSelect').select2({
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

                // Form validation
                $('#menuForm').on('submit', function(e) {
                    const name = $('input[name="name"]').val().trim();
                    const divisionIds = $('#divisionSelect').val();

                    if (name === '') {
                        e.preventDefault();
                        Swal.fire({
                            icon: 'error',
                            title: 'Form Tidak Lengkap',
                            text: 'Nama menu wajib diisi!',
                        });
                        return false;
                    }

                    if (!divisionIds || divisionIds.length === 0) {
                        e.preventDefault();
                        Swal.fire({
                            icon: 'error',
                            title: 'Divisi Belum Dipilih',
                            text: 'Pilih minimal satu divisi!',
                        });
                        return false;
                    }
                });
            });
        </script>
    </main>
</x-app-layout>