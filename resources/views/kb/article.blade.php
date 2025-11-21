<x-app-layout>
    <main class="flex-1 p-6">

        <span class="flex items-center space-x-2 text-gray-700 mb-6">
            <a href="{{ route('dashboard')}}">
                <i class="fa fa-home"></i>
                <span class="hover:underline">Home</span>
                <span> / </span>
            </a>
            <a href="{{ route('kb.index') }}" class="hover:underline">Knowledge Base</a>
            <span>/</span>
            <a href="{{ route('kb.category', $category) }}" class="hover:underline">{{ $category->name }}</a>
            <span>/</span>
            <span>{{ $article->title }}</span>
        </span>

        @if (session('status'))
        <div class="mb-4 rounded-md bg-green-50 p-4 ring-1 ring-inset ring-green-200">
            <p class="text-sm text-green-800">{{ session('status') }}</p>
        </div>
        @endif

        @php
        $user = auth()->user();
        $isSuperAdmin = false;
        $appIsLocal = app()->environment('local'); // ✅ cek apakah environment 'local'

        if ($user) {
        // Super Admin = role_id 1, Admin Sistem = role_id 2
        $isSuperAdmin = in_array($user->role_id, [1, 2]);
        }
        @endphp

        <div class="bg-white p-6 rounded-md shadow">
            <header class="mb-4 pb-4 border-b">
                <h1 class="text-3xl font-bold text-gray-800">{{ $article->title }}</h1>

                @if ($isSuperAdmin)
                <div class="mt-3 flex gap-2">
                    <a href="{{ route('kb.article.edit', [$category, $article]) }}"
                        class="inline-flex items-center rounded-md bg-white px-3 py-1.5 text-xs font-medium text-gray-700
                                      ring-1 ring-inset ring-gray-300 hover:bg-gray-50">Edit</a>
                    <form method="POST" action="{{ route('kb.article.destroy', [$category, $article]) }}"
                        onsubmit="return confirm('Hapus artikel ini?')">
                        @csrf @method('DELETE')
                        <button
                            class="inline-flex items-center rounded-md bg-white px-3 py-1.5 text-xs font-medium text-red-600
                                               ring-1 ring-inset ring-gray-300 hover:bg-red-50">Hapus</button>
                    </form>
                </div>
                @endif

                @if ($article->summary)
                <p class="mt-3 text-gray-600">{{ $article->summary }}</p>
                @endif

                <p class="mt-1 text-xs text-gray-400">{{ number_format($article->views ?? 0) }} views</p>
            </header>

            {{-- Konten WYSIWYG (HTML) --}}
            <article class="prose max-w-none">
                {!! $article->content !!}
            </article>

            {{-- Lampiran + viewer --}}
            @if ($article->attachments->count())
            <section class="mt-8">
                <h2 class="text-lg font-semibold text-gray-900 mb-3">Lampiran</h2>

                <ul class="space-y-4">
                    @foreach ($article->attachments as $att)
                    @php
                    $url = $att->url();
                    $ext = $att->ext();
                    @endphp

                    <li class="rounded-lg border border-gray-200 p-4">
                        <div class="flex items-center justify-between gap-3">
                            <div class="text-sm text-gray-700">
                                <strong class="text-gray-900">{{ strtoupper($ext) }}</strong>
                                — <a href="{{ $url }}" target="_blank"
                                    class="hover:underline">{{ $att->original_name }}</a>
                                <span class="text-xs text-gray-400">({{ $att->sizeKB() }})</span>
                            </div>
                            <a href="{{ $url }}" target="_blank"
                                class="inline-flex items-center rounded-md bg-white px-3 py-1.5 text-xs font-medium
                                                  text-gray-700 ring-1 ring-inset ring-gray-300 hover:bg-gray-50">
                                Download
                            </a>
                        </div>

                        <!-- {{-- PDF viewer --}} -->
                        @if ($ext === 'pdf')
                        <div class="mt-3">
                            <iframe src="{{ $url }}#toolbar=1&navpanes=0&scrollbar=1"
                                class="w-full h-[640px] rounded-md ring-1 ring-gray-200"
                                title="PDF Viewer"></iframe>
                        </div>

                        <!-- {{-- DOC/DOCX viewer via Office (butuh URL publik) --}} -->
                        @elseif (in_array($ext, ['doc', 'docx']))
                        <div class="mt-3">
                            @php
                            $publicUrl = urlencode($url);
                            $officeViewer = "https://view.officeapps.live.com/op/embed.aspx?src={$publicUrl}";
                            @endphp

                            @if ($appIsLocal)
                            <div
                                class="rounded-md bg-yellow-50 p-3 ring-1 ring-yellow-200 text-sm text-yellow-800">
                                Pratinjau DOC/DOCX butuh URL publik. Di lingkungan lokal,
                                gunakan
                                tombol
                                <b>Download</b>.
                            </div>
                            @else
                            <iframe src="{{ $officeViewer }}"
                                class="w-full h-[640px] rounded-md ring-1 ring-gray-200"
                                title="DOC/DOCX Viewer"></iframe>
                            @endif
                        </div>
                        @endif
                    </li>
                    @endforeach
                </ul>
            </section>
            @endif
        </div>
    </main>
</x-app-layout>