<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="antialiased min-vh-100 d-flex align-items-center justify-content-center bg-gradient-to-tr from-purple-800 via-purple-600 to-indigo-700"
    style="background-image: url('/image/sps-ngawi-manufaktur.jpg'); 
           background-size: cover; 
           background-position: center; 
           background-repeat: no-repeat;">

    <div style="
        position: absolute;
        inset: 0;
        background: linear-gradient(to right, rgba(67, 56, 202, 0.55), rgba(124, 58, 237, 0.55));
        backdrop-filter: blur(2px);
        z-index: 1;
    "></div>

    <div class="d-flex flex-column align-items-center gap-3"
        style="position: relative; z-index: 5;">

        <div class="d-flex align-items-center gap-2">
            <img src="/image/pt-surya-pangan-semesta-logo-removebg-preview.png" alt="" width="110px">
            <div style="
                width: 100%; 
                max-width: 500px; 
                padding: 10px 0;
                text-align: center;
                color: white;
                font-size: 2rem;
                font-weight: 600;
            "> PT. Surya Pangan Semesta
            </div>
        </div>

        <div class="card shadow-lg border-0 rounded-4 p-4"
            style="
                width: 100%; 
                max-width: 500px; 
                background: rgba(255,255,255,0.1); 
                backdrop-filter: blur(10px);
            ">
            {{ $slot }}
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
    @if (session('logout_success'))
    <script>
        Swal.fire({
            icon: 'success',
            title: 'Berhasil Logout',
            text: '{{ session("logout_success") }}',
            timer: 1500,
            showConfirmButton: false
        });
    </script>
    @endif
</body>

</html>