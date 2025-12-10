<!-- User edit page -->
<x-app-layout>
    <x-breadcrumb />

    <div class="bg-white p-6 space-y-6 rounded-md shadow">
        <!-- Header -->
        <div class="mb-6">
            <h2 class="text-2xl font-semibold text-gray-800">Edit User</h2>
        </div>

        <!-- Form -->
        <form action="{{ route('users.update', $user->id) }}" method="POST" enctype="multipart/form-data"
            class="">
            @csrf
            @method('PUT')


            <!-- Avatar Upload -->
            <div class="flex flex-col items-center space-y-3">
                <!-- Preview Avatar -->
                <img id="avatarPreview"
                    src="{{ $user->profile_photo_path 
                    ? asset('storage/' . $user->profile_photo_path) 
                    : 'https://ui-avatars.com/api/?name=' . urlencode($user->name) }}"
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
                    <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}"
                        class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm 
                           focus:outline-none focus:ring focus:ring-purple-500 focus:border-purple-500">
                </div>

                <!-- Username -->
                <div>
                    <label for="username" class="block text-sm font-medium text-gray-700">Username</label>
                    <input type="text" id="username" name="username" value="{{ old('username', $user->username) }}"
                        class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm 
                           focus:outline-none focus:ring focus:ring-purple-500 focus:border-purple-500">
                </div>

                <!-- Email -->
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                    <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}"
                        class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm 
                           focus:outline-none focus:ring focus:ring-purple-500 focus:border-purple-500">
                </div>

                <!-- position -->
                <div class="mb-3">
                    <label for="position_id" class="form-label">Pilih Posisi</label>
                    <select name="position_id" id="position_id" class="form-select" required>
                        <option value="" disabled>Pilih Posisi</option>
                        @foreach ($positions as $position)
                        <option value="{{ $position->id }}"
                            {{ old('position_id', $user->position_id) == $position->id ? 'selected' : '' }}>
                            {{ $position->name ?? '' }} - {{ $position->division->name ?? '' }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <!-- role -->
                <div class="mb-3">
                    <label for="role_id" class="form-label">Pilih Role</label>
                    <select name="role_id" id="role_id" class="form-select" required>
                        <option value="">Tidak ada</option>
                        @foreach ($roles as $role)
                        <option value="{{ $role->id }}"
                            {{ old('role_id', $user->master_role_id) == $role->id ? 'selected' : '' }}>
                            {{ $role->name ?? '' }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <!-- Site -->
                <div class="mb-3">
                    <label for="master_site_id" class="form-label">Pilih Site</label>
                    <select name="master_site_id" id="master_site_id" class="form-select">
                        <option value="">Tidak ada</option>
                        @foreach ($sites as $site)
                        <option value="{{ $site->id }}"
                            {{ $user->master_site_id == $site->id ? 'selected' : '' }}>
                            {{ $site->name }}
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
            <div class="flex justify-between items-center mt-6">
                <div class="flex justify-end">
                    <button type="submit"
                        class="px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded shadow">
                        Simpan
                    </button>
                </div>
            </div>
        </form>
        <!-- Tombol Reset Password -->
        <form action="{{ route('password.reset', $user->id) }}" method="POST"
            onsubmit="return confirm('Yakin reset password user ini?')">
            @csrf
            @method('PUT')
            <button type="submit"
                class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded">
                Reset Password
            </button>
        </form>
    </div>

    <!-- Script Preview -->
    <script>
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