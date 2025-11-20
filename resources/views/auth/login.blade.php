<x-guest-layout>
    <div x-data="loginForm()">

        <div class="text-center mb-4">
            <i class="fa-solid fa-user-lock text-white fs-1"></i>
            <h4 class="text-white mt-2">Login Sistem</h4>
        </div>

        <form @submit.prevent="submitLogin">
            @csrf

            <div class="mb-3">
                <input
                    x-model="form.username"
                    type="text"
                    name="username"
                    class="form-control bg-transparent text-white border-white placeholder-white"
                    :class="{ 'is-invalid': errors.username }"
                    placeholder="Username"
                    autocomplete="username"
                    required>
                <div x-show="errors.username" class="text-danger small mt-1" x-text="errors.username"></div>
            </div>

            <div class="mb-3" x-data="{ show: false }" style="position: relative;">
                <input
                    :type="show ? 'text' : 'password'"
                    x-model="form.password"
                    name="password"
                    class="form-control bg-transparent text-white border-white placeholder-white pe-5"
                    :class="{ 'is-invalid': errors.password }"
                    placeholder="Password"
                    autocomplete="current-password"
                    required>

                <button
                    type="button"
                    @click="show = !show"
                    style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); 
                        background: transparent; border: none; color: black;">
                    <i :class="show ? 'bi bi-eye-slash' : 'bi bi-eye'"></i>
                </button>

                <div x-show="errors.password" class="text-danger small mt-1" x-text="errors.password"></div>
            </div>

            <!-- Remember Me -->
            <div class="block mt-4">
                <label for="remember_me" class="inline-flex items-center">
                    <input id="remember_me" type="checkbox" class="rounded dark:bg-white-900 border-white-300 dark:border-white-700 text-white-600 shadow-sm focus:ring-indigo-500 dark:focus:ring-indigo-600 dark:focus:ring-offset-white-800" name="remember">
                    <span class="ms-2 text-sm text-white-600 dark:text-white-400">{{ __('Remember me') }}</span>
                </label>
            </div>

            <div class="flex items-center justify-end mt-4">
                @if (Route::has('password.request'))
                <a class="underline text-sm text-white-600 dark:text-white-400 hover:text-white-900 dark:hover:text-white-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-white-800" href="{{ route('password.request') }}">
                    {{ __('Forgot your password?') }}
                </a>
                @endif
            </div>

            <button
                type="submit"
                class="btn btn-light w-100 py-2 rounded-3 shadow-sm mt-2 d-flex justify-content-center align-items-center gap-2 position-relative text-center">

                <template x-if="!isProcessing">
                    <span class="text-black d-flex align-items-center gap-2">
                        <i class="fa-solid fa-right-to-bracket"></i>
                        Login
                    </span>
                </template>

                <template x-if="isProcessing">
                    <span class="text-black d-flex align-items-center gap-2">
                        <i class="fa-solid fa-spinner fa-spin"></i>
                        Memproses...
                    </span>
                </template>
            </button>
        </form>
    </div>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function loginForm() {
            return {
                form: {
                    username: '',
                    password: ''
                },
                errors: {},
                isProcessing: false,

                async submitLogin() {
                    this.isProcessing = true;
                    this.errors = {};

                    try {
                        const formData = new URLSearchParams();
                        formData.append('username', this.form.username);
                        formData.append('password', this.form.password);
                        formData.append('_token', document.querySelector('meta[name="csrf-token"]').content);

                        const response = await fetch('{{ route("login") }}', {
                            method: 'POST',
                            headers: {
                                'Accept': 'application/json'
                            },
                            body: formData
                        });

                        if (response.redirected) {

                            await Swal.fire({
                                icon: 'success',
                                title: 'Selamat Datang 🎉',
                                text: 'Login berhasil! Mengarahkan ke dashboard...',
                                showConfirmButton: false,
                                timer: 1800,
                                background: '#fff',
                                color: '#000'
                            });

                            window.location.href = response.url;
                            return;
                        }

                        const data = await response.json();

                        if (!response.ok) {
                            this.errors = data.errors || {};
                            Swal.fire({
                                icon: 'error',
                                title: 'Login Gagal',
                                text: 'Username atau password salah.'
                            });
                        }

                    } catch (e) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Kesalahan Server',
                            text: 'Tidak dapat terhubung ke server.'
                        });
                    } finally {
                        this.isProcessing = false;
                    }
                }
            }
        }
    </script>
    @endpush
</x-guest-layout>