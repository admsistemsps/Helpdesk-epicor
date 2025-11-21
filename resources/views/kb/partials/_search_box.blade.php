{{-- KB: Search box reusable (Breeze light) --}}
{{-- Props yang dipakai:
     - $action : route tujuan (GET)
     - $q      : nilai query saat ini (opsional)
     - $placeholder : placeholder input (opsional)
--}}
<form method="GET" action="{{ $action }}" class="w-full">
    <div class="relative">
        <input type="text" name="q" value="{{ $q ?? '' }}" placeholder="{{ $placeholder ?? 'Cari…' }}"
            class="block w-full rounded-md border-gray-300 pr-24
                   focus:border-indigo-500 focus:ring-indigo-500" />
        <button
            class="absolute right-1 top-1/2 -translate-y-1/2 inline-flex items-center
                   rounded-md bg-white px-3 py-1.5 text-sm font-semibold text-gray-700
                   ring-1 ring-inset ring-gray-300 hover:bg-gray-50">
            Cari
        </button>
    </div>
</form>
