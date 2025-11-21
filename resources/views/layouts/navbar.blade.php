<!-- resources/views/layouts/navbar.blade.php -->
<div class="flex items-center justify-between w-full h-full px-2 sm:px-4">
    <!-- Hamburger Button (Mobile Only) -->
    <button @click="sidebarOpen = !sidebarOpen" class="md:hidden text-gray-700 hover:text-gray-900 focus:outline-none mr-2">
        <i class="fa-solid fa-bars text-lg"></i>
    </button>

    <div class="flex items-center space-x-2 sm:space-x-3 flex-1 min-w-0">
        <a href="https://beraskediri.com/" target="_blank" rel="noopener noreferrer" class="flex-shrink-0">
            <img src="{{ asset('storage/pt-surya-pangan-semesta-logo.jpg') }}" alt="Logo" class="w-8 h-8 sm:w-10 sm:h-10 object-contain">
        </a>
        <div class="flex flex-col leading-tight min-w-0 flex-1">
            <span class="text-xs sm:text-sm font-semibold text-gray-800 truncate">
                PT. Surya Pangan Semesta
            </span>

            <!-- Mobile - Ultra compact -->
            <span class="text-[10px] sm:text-xs text-gray-600 sm:hidden truncate">
                Bringin, Kec. Pagu Kediri
            </span>

            <!-- Tablet - Moderate -->
            <div class="hidden sm:flex md:hidden flex-col leading-tight">
                <span class="text-xs text-gray-600 truncate">
                    Bringin, Kec. Pagu
                </span>
                <span class="text-xs text-gray-600 truncate">
                    Kediri
                </span>
            </div>

            <!-- Desktop - Full -->
            <div class="hidden md:flex flex-col leading-tight">
                <span class="text-xs text-gray-600">
                    Jl. Dusun Bringin No.300, Bringin, Wonosari,
                </span>
                <span class="text-xs text-gray-600">
                    Kec. Pagu, Kabupaten Kediri, Jawa Timur 64183
                </span>
            </div>
        </div>
    </div>

    <div class="flex items-center bg-white/80 border rounded-full pl-2 sm:pl-3 shadow-sm space-x-1 sm:space-x-2 flex-shrink-0">
        <div class="hidden sm:flex items-center">
            <i class="fa-regular fa-clock text-gray-600 text-sm mr-2"></i>
            <span id="clock" class="text-xs font-medium text-gray-800"></span>
        </div>

        <div class="relative" x-data="{ profileOpen: false }">
            <button @click="profileOpen = !profileOpen" class="flex items-center focus:outline-none">
                @php
                $avatarPath = Auth::user()->profile_photo_path;
                $avatar = $avatarPath
                ? asset('storage/' . $avatarPath)
                : 'https://ui-avatars.com/api/?name=' . urlencode(Auth::user()->name);
                @endphp
                <img class="w-8 h-8 sm:w-10 sm:h-10 rounded-full border object-cover" src="{{ $avatar }}" alt="Avatar">
            </button>

            <div x-show="profileOpen" @click.away="profileOpen = false" x-transition
                class="absolute right-0 mt-2 w-44 bg-white shadow-lg rounded-md overflow-hidden z-50">
                <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-xs text-gray-700 hover:bg-gray-100">
                    Profil
                </a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                        class="w-full text-left px-4 py-2 text-xs text-red-600 hover:bg-gray-100">
                        Logout
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>