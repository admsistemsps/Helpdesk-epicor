<x-app-layout>
    <main class="flex-1 p-6">
        @php
        $user = auth()->user();
        $isSuperAdmin = false;

        if ($user) {
        // role_id 1 = Super Admin, role_id 2 = Admin Sistem
        $isSuperAdmin = in_array($user->role_id, [1, 2]);
        }
        @endphp

        <span class="flex items-center space-x-2 text-gray-700 mb-6">
            <a href="{{ route('dashboard')}}">
                <i class="fa fa-home"></i>
                <span class="hover:underline">Home</span>
                <span> / </span>
            </a>
            <a href="{{ route('kb.index') }}" class="hover:underline">Knowledge Base</a>
            <span>/</span>
            <span>{{ $category->name }}</span>
        </span>

        <div class="bg-white p-6 rounded-md shadow">
            {{-- Flash Message --}}
            @if (session('status'))
            <div class="mb-6 rounded-md bg-green-50 p-4 ring-1 ring-inset ring-green-200">
                <p class="text-sm text-green-800">{{ session('status') }}</p>
            </div>
            @endif

            {{-- Header + search + tombol tambah --}}
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
                <div>
                    <h2 class="text-2xl font-semibold text-gray-800">{{ $category->name }}</h2>
                    @if ($category->description)
                    <p class="mt-1 text-sm text-gray-600">{{ $category->description }}</p>
                    @endif
                </div>

                <div class="flex items-center gap-3 w-full md:w-auto">
                    <div class="w-full md:w-[520px]">
                        @include('kb.partials._search_box', [
                        'action' => route('kb.category', $category),
                        'q' => $q ?? '',
                        'placeholder' => 'Cari artikel dalam ' . $category->name . '…',
                        ])
                    </div>

                    @if ($isSuperAdmin)
                    <a href="{{ route('kb.article.create', $category) }}"
                        class="inline-flex items-center rounded-md bg-purple-600 px-4 py-2 text-sm font-semibold text-white shadow-sm
                              hover:bg-purple-700 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-purple-600 whitespace-nowrap">
                        + Tambah Artikel
                    </a>
                    @endif
                </div>
            </div>

            {{-- Info hasil pencarian --}}
            @if (!empty($q))
            <div class="mb-4 rounded-md bg-blue-50 p-3 ring-1 ring-inset ring-blue-200">
                <p class="text-sm text-blue-800">
                    Menampilkan hasil pencarian untuk "<strong>{{ $q }}</strong>" dalam kategori <strong>{{ $category->name }}</strong>
                    <span class="text-blue-600">({{ $articles->total() }} artikel ditemukan)</span>
                </p>
            </div>
            @endif

            {{-- Grid artikel --}}
            <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
                @forelse ($articles as $a)
                <div class="rounded-xl border border-gray-200 bg-white shadow-sm hover:shadow transition">
                    <a href="{{ route('kb.article', [$category, $a]) }}" class="block p-5">
                        <h3 class="text-base font-semibold text-gray-900 line-clamp-2">{{ $a->title }}</h3>
                        @if ($a->summary)
                        <p class="mt-2 text-sm text-gray-600 line-clamp-2">{{ $a->summary }}</p>
                        @endif
                        <p class="mt-3 text-xs text-gray-400">{{ number_format($a->views ?? 0) }} views</p>
                    </a>

                    @if ($isSuperAdmin)
                    <div class="px-5 pb-5 -mt-2 flex gap-2">
                        <a href="{{ route('kb.article.edit', [$category, $a]) }}"
                            class="inline-flex items-center rounded-md bg-white px-3 py-1.5 text-xs font-medium text-gray-700
                                   ring-1 ring-inset ring-gray-300 hover:bg-gray-50">Edit</a>
                        <form method="POST" action="{{ route('kb.article.destroy', [$category, $a]) }}"
                            onsubmit="return confirm('Hapus artikel ini?')">
                            @csrf @method('DELETE')
                            <button
                                class="inline-flex items-center rounded-md bg-white px-3 py-1.5 text-xs font-medium text-red-600
                                       ring-1 ring-inset ring-gray-300 hover:bg-red-50">Hapus</button>
                        </form>
                    </div>
                    @endif
                </div>
                @empty
                <div class="sm:col-span-2 lg:col-span-3">
                    <div class="rounded-lg border border-dashed border-gray-300 p-8 text-center">
                        @if (!empty($q))
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                        <p class="mt-2 text-sm font-medium text-gray-900">Tidak ada hasil ditemukan</p>
                        <p class="mt-1 text-sm text-gray-600">
                            Tidak ada artikel yang cocok dengan pencarian "<strong>{{ $q }}</strong>" dalam kategori ini.
                        </p>
                        <div class="mt-4">
                            <a href="{{ route('kb.category', $category) }}"
                                class="text-sm font-medium text-purple-600 hover:text-purple-500">
                                Tampilkan semua artikel →
                            </a>
                        </div>
                        @else
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <p class="mt-2 text-sm text-gray-600">Belum ada artikel pada kategori ini.</p>
                        @endif
                    </div>
                </div>
                @endforelse
            </div>

            {{-- Pagination --}}
            @if ($articles->hasPages())
            <div class="mt-6">
                {{ $articles->links() }}
            </div>
            @endif
        </div>
    </main>
</x-app-layout>