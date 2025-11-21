<x-app-layout>
    <main class="flex-1 p-6">
        {{-- Breadcrumb --}}
        <span class="flex items-center space-x-2 text-gray-700 mb-6">
            <a href="{{ route('kb.index') }}" class="hover:underline">Knowledge Base</a>
            <span>/</span>
            <span>Hasil Pencarian</span>
        </span>

        <div class="bg-white p-6 rounded-md shadow">
            {{-- Judul + form search --}}
            <div class="mb-6">
                <h2 class="text-2xl font-semibold text-gray-800 mb-3">Hasil pencarian untuk “{{ $q }}”
                </h2>
                @include('kb.partials._search_box', [
                'action' => route('kb.index'),
                'q' => $q,
                'placeholder' => 'Cari artikel di semua kategori…',
                ])
            </div>

            {{-- Hasil --}}
            <div class="">
                <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
                    @forelse ($articles as $a)
                    <a href="{{ route('kb.article', [$a->category, $a]) }}"
                        class="block rounded-xl border border-gray-200 bg-white p-5 shadow-sm hover:shadow transition">
                        <div class="text-xs text-gray-500 mb-1">{{ $a->category->name }}</div>
                        <h3 class="text-base font-semibold text-gray-900 line-clamp-2">{{ $a->title }}</h3>
                        @if ($a->summary)
                        <p class="mt-2 text-sm text-gray-600 line-clamp-2">{{ $a->summary }}</p>
                        @endif
                        <p class="mt-3 text-xs text-gray-400">{{ number_format($a->views ?? 0) }} views</p>
                    </a>
                    @empty
                    <div class="sm:col-span-2 lg:col-span-3">
                        <div class="rounded-lg border border-dashed border-gray-300 p-8 text-center">
                            <p class="text-sm text-gray-600">Tidak ada artikel yang cocok.</p>
                        </div>
                    </div>
                    @endforelse
                </div>

                <div class="mt-6">
                    {{ $articles->links() }}
                </div>
            </div>
        </div>
    </main>
</x-app-layout>