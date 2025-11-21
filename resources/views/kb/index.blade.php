<x-app-layout>
    <main class="flex-1 p-6">
        <div class="flex items-center space-x-2 text-gray-700 mb-6">
            <a href="{{ route('dashboard') }}">
                <span class="flex items-center space-x-2 text-gray-700 mb-6">
                    <i class="fa-solid fa-house text-sm"></i>
                    <span class="hover:underline">Home</span>
                    <span> / </span>
                </span>
            </a>
            <span class="flex items-center space-x-2 text-gray-700 mb-6">
                <span>Knowledge Base</span>
            </span>
        </div>

        <div class="bg-white p-6 rounded-md shadow">

            @if (session('status'))
            <div class="mb-6 rounded-md bg-green-50 p-4 ring-1 ring-inset ring-green-200">
                <p class="text-sm text-green-800">{{ session('status') }}</p>
            </div>
            @endif


            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
                <h2 class="text-2xl font-semibold text-gray-800">Kategori</h2>

                <div class="w-full sm:w-auto sm:min-w-[320px]">
                    @include('kb.partials._search_box', [
                    'action' => route('kb.index'),
                    'q' => request('q'),
                    'placeholder' => 'Cari artikel di semua kategori…',
                    ])
                </div>
            </div>

            <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
                @forelse ($categories as $cat)
                <a href="{{ route('kb.category', $cat) }}"
                    class="block rounded-xl border border-gray-200 bg-white p-5 shadow-sm hover:shadow-md transition-shadow">
                    <div class="flex items-start justify-between gap-3">
                        <h3 class="text-lg font-semibold text-gray-900 flex-1">{{ $cat->name }}</h3>
                        <span class="inline-flex items-center rounded-full bg-purple-50 px-2.5 py-1 text-xs font-medium text-purple-700 whitespace-nowrap">
                            {{ $cat->articles_count }} artikel
                        </span>
                    </div>
                    @if ($cat->description)
                    <p class="mt-2 text-sm text-gray-600 line-clamp-2">{{ $cat->description }}</p>
                    @endif
                </a>
                @empty
                <div class="sm:col-span-2 lg:col-span-3">
                    <div class="rounded-lg border border-dashed border-gray-300 p-8 text-center">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                        </svg>
                        <p class="mt-2 text-sm font-medium text-gray-900">Belum ada kategori</p>
                        <p class="mt-1 text-sm text-gray-600">Kategori artikel akan muncul di sini.</p>
                    </div>
                </div>
                @endforelse
            </div>
        </div>
    </main>
</x-app-layout>