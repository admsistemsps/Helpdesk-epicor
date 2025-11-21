@props(['color', 'icon', 'title', 'value'])

<div class="bg-gradient-to-r {{ $color }} text-white p-5 rounded-2xl shadow flex items-center justify-between">
    <div>
        <p class="text-sm opacity-80">{{ $title }}</p>
        <h2 class="text-3xl font-bold">{{ $value }}</h2>
    </div>
    <i class="fa-solid {{ $icon }} text-3xl opacity-70"></i>
</div>