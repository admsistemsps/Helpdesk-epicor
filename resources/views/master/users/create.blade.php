<!-- User Index Show -->
<x-app-layout>
    <x-breadcrumb />

    <!-- Header -->
    <div class="mb-6">
        <h2 class="text-2xl font-semibold text-gray-800">Tambah User</h2>
    </div>

    <!-- Form -->
    <form action="{{ route('users.store') }}" method="POST" enctype="multipart/form-data"
        class="bg-white p-6 space-y-6 rounded-md shadow">
        @csrf


        <!-- Avatar Upload -->
        <div class="flex flex-col items-center space-y-3">
            <!-- Preview Avatar -->
            <img id="avatarPreview"
                src="https://ui-avatars.com/api/?name="
                class="w-32 h-32 rounded-full object-cover border shadow">

            <!-- Upload Button -->
            <label for="avatar"
                class="cursor-pointer px-3 py-1 bg-purple-600 text-white text-sm rounded hover:bg-purple-700">
                Pilih Foto
            </label>
            <input type="file" id="avatar" name="avatar" accept="image/*" class="hidden">

            @error('avatar')
            <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Grid 3 kolom -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <!-- Nama -->
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700">Nama</label>
                <input type="text" id="name" name="name" value="{{ old('name') }}"
                    class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm 
                       focus:outline-none focus:ring focus:ring-purple-500 focus:border-purple-500">
            </div>

            <!-- Username -->
            <div>
                <label for="username" class="block text-sm font-medium text-gray-700">Username</label>
                <input type="text" id="username" name="username" value="{{ old('username') }}"
                    class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm 
                       focus:outline-none focus:ring focus:ring-purple-500 focus:border-purple-500">
            </div>

            <!-- Email -->
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                <input type="email" id="email" name="email" value="{{ old('email') }}"
                    class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm 
                       focus:outline-none focus:ring focus:ring-purple-500 focus:border-purple-500">
            </div>

            <!-- Password -->
            <div>
                <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                <input type="password" id="password" name="password"
                    class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm 
                       focus:outline-none focus:ring focus:ring-purple-500 focus:border-purple-500">
            </div>

            <!-- Konfirmasi Password -->
            <div>
                <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Konfirmasi Password</label>
                <input type="password" id="password_confirmation" name="password_confirmation"
                    class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm 
                       focus:outline-none focus:ring focus:ring-purple-500 focus:border-purple-500">
            </div>

        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <!-- Position -->
            <div class="mt-1">
                <label for="position_id" class="form-label">Pilih Posisi</label>
                <select name="position_id" id="position_id" class="form-select" required>
                    <option value="" selected>Tidak ada</option>
                    @foreach ($positions as $position)
                    <option value="{{ $position->id }}">
                        {{ $position->name ?? '' }} - {{ $position->division->name ?? '' }}
                    </option>
                    @endforeach
                </select>
            </div>

            <!-- Role -->
            <div class="mt-1">
                <label for="role_id" class="form-label">Pilih Role</label>
                <select name="role_id" id="role_id" class="form-select" required>
                    <option value="" selected>Tidak ada</option>
                    @foreach ($roles as $role)
                    <option value="{{ $role->id }}">
                        {{ $role->name ?? '' }}
                    </option>
                    @endforeach
                </select>
            </div>

            <!-- Site -->
            <div class="mt-1">
                <label for="master_site_id" class="form-label">Pilih Site</label>
                <select name="master_site_id" id="master_site_id" class="form-select">
                    <option value="" selected>Tidak ada</option>
                    @foreach ($sites as $site)
                    <option value="{{ $site->id }}">
                        {{ $site->name ?? '' }}
                    </option>
                    @endforeach
                </select>
            </div>
        </div>

        <!-- Checkbox Nonaktif -->
        <div class="flex items-center">
            <input type="checkbox" id="is_active" name="is_active" value="1"
                {{ old('is_active') == '1' ? 'checked' : '' }}
                class="h-4 w-4 text-purple-600 border-gray-300 rounded focus:ring-purple-500">
            <label for="is_active" class="ml-2 block text-sm text-gray-700">
                Nonaktif
            </label>
        </div>

        <!-- Tombol -->
        <div class="flex justify-end">
            <button type="submit"
                class="px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded shadow">
                Simpan
            </button>
        </div>
    </form>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            new Choices('#position_id', {
                searchEnabled: true,
                itemSelectText: '',
                placeholderValue: 'Pilih posisi...',
            });

            new Choices('#role_id', {
                searchEnabled: true,
                itemSelectText: '',
                placeholderValue: 'Pilih role...',
            });
            new Choices('#master_site_id', {
                searchEnabled: true,
                itemSelectText: '',
                placeholderValue: 'Pilih site...',
            });
        });
        document.getElementById('avatar').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();

                reader.onload = function(e) {
                    document.getElementById('avatarPreview').setAttribute('src', e.target.result);
                };

                reader.readAsDataURL(file);
            }
        });
    </script>
</x-app-layout>