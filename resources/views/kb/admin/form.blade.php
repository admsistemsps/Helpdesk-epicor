<x-app-layout>
    <main class="flex-1 p-6">
        {{-- Breadcrumb --}}
        <span class="flex items-center space-x-2 text-gray-700 mb-6">
            <a href="{{ route('kb.index') }}" class="hover:underline">Knowledge Base</a>
            <span>/</span>
            <a href="{{ route('kb.category', $category) }}" class="hover:underline">{{ $category->name }}</a>
            <span>/</span>
            <span>{{ $mode === 'create' ? 'Buat Artikel' : 'Edit Artikel' }}</span>
        </span>

        <div class="mb-6">
            <h2 class="text-2xl font-semibold text-gray-800">
                {{ $mode === 'create' ? 'Buat Artikel Baru' : 'Edit Artikel' }}
            </h2>
        </div>


        {{-- Error list --}}
        @if ($errors->any())
        <div class="mb-4 rounded-md bg-red-50 p-4 ring-1 ring-inset ring-red-200">
            <ul class="list-disc list-inside text-sm text-red-800">
                @foreach ($errors->all() as $err)
                <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <form method="POST"
            action="{{ $mode === 'create' ? route('kb.article.store', $category) : route('kb.article.update', [$category, $article]) }}"
            enctype="multipart/form-data" class="bg-white p-6 space-y-6 rounded-md shadow">
            @csrf
            @if ($mode === 'edit')
            @method('PUT')
            @endif

            {{-- Judul --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Judul</label>
                <input type="text" name="title" value="{{ old('title', $article->title) }}"
                    class="block w-full rounded-md border-gray-300 shadow-sm
                               focus:outline-none focus:ring focus:ring-purple-500 focus:border-purple-500" />
            </div>

            {{-- Ringkasan --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Ringkasan (opsional)</label>
                <input type="text" name="summary" value="{{ old('summary', $article->summary) }}"
                    class="block w-full rounded-md border-gray-300 shadow-sm
                               focus:outline-none focus:ring focus:ring-purple-500 focus:border-purple-500" />
            </div>

            {{-- Konten (TinyMCE) --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Konten</label>
                <textarea id="tinymce-editor" name="content" rows="15">{{ old('content', $article->content) }}</textarea>
                <p class="mt-1 text-xs text-gray-500">Gambar bisa ditempel (paste) / drag&drop ke editor. Maks
                    10MB per
                    gambar.</p>
            </div>

            {{-- Lampiran PDF/DOC/DOCX --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Lampiran dokumen
                    (PDF/DOC/DOCX)</label>
                <input type="file" name="attachments[]" multiple
                    accept=".pdf,.doc,.docx,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document,application/pdf"
                    class="block w-full cursor-pointer rounded-md border border-dashed border-gray-300 p-3
                               focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2">
                <p class="mt-1 text-xs text-gray-500">Disimpan di
                    <code>storage/app/public/kb/attachments</code>, maks
                    30MB
                    per file.
                </p>
            </div>

            {{-- Daftar lampiran (mode edit) --}}
            @if ($mode === 'edit' && $article->attachments->count())
            <div class="rounded-md border border-gray-200 p-4">
                <div class="text-sm font-medium text-gray-900 mb-2">Lampiran saat ini</div>
                <ul class="space-y-2">
                    @foreach ($article->attachments as $att)
                    <li class="flex items-center justify-between gap-3">
                        <div class="text-sm text-gray-700">
                            <a href="{{ $att->url() }}" target="_blank"
                                class="hover:underline">{{ $att->original_name }}</a>
                            <span class="text-xs text-gray-400">({{ strtoupper($att->ext()) }},
                                {{ $att->sizeKB() }})</span>
                        </div>
                        <label class="inline-flex items-center gap-2 text-sm">
                            <input type="checkbox" name="delete_attachment_ids[]"
                                value="{{ $att->id }}"
                                class="rounded border-gray-300 text-red-600 focus:ring-red-500">
                            <span class="text-gray-600">Hapus</span>
                        </label>
                    </li>
                    @endforeach
                </ul>
            </div>
            @endif

            {{-- Actions --}}
            <div class="flex items-center gap-3">
                <button
                    class="inline-flex items-center rounded-md bg-purple-600 px-4 py-2 text-sm font-semibold text-white shadow-sm
                               hover:bg-purple-700 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2
                               focus-visible:outline-purple-600">
                    {{ $mode === 'create' ? 'Simpan' : 'Simpan Perubahan' }}
                </button>
                <a href="{{ $mode === 'create' ? route('kb.category', $category) : route('kb.article', [$category, $article]) }}"
                    class="inline-flex items-center rounded-md bg-white px-4 py-2 text-sm font-semibold text-gray-700
                              ring-1 ring-inset ring-gray-300 hover:bg-gray-50">
                    Batal
                </a>
            </div>
        </form>
    </main>

    {{-- TinyMCE open-source (no API key) --}}
    <script src="https://cdn.jsdelivr.net/npm/tinymce@6/tinymce.min.js" referrerpolicy="origin"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const csrf = '{{ csrf_token() }}';

            tinymce.init({
                selector: '#tinymce-editor',
                height: 520,
                menubar: 'file edit view insert format tools table help',
                toolbar: 'undo redo | blocks | bold italic underline strikethrough | ' +
                    'alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | ' +
                    'link image media table | removeformat code',
                plugins: 'link lists table image media code paste autoresize',
                paste_data_images: true,
                automatic_uploads: true,
                images_upload_handler: function(blobInfo, progress) {
                    return new Promise(function(resolve, reject) {
                        const xhr = new XMLHttpRequest();
                        xhr.open('POST', '{{ route('kb.tinymce.upload') }}');
                        xhr.setRequestHeader('X-CSRF-TOKEN', csrf);

                        xhr.upload.onprogress = function(e) {
                            if (e.lengthComputable) progress(e.loaded / e.total * 100);
                        };

                        xhr.onload = function() {
                            if (xhr.status < 200 || xhr.status >= 300) return reject('HTTP Error: ' + xhr.status);
                            let json;
                            try {
                                json = JSON.parse(xhr.responseText);
                            } catch (e) {
                                return reject('Invalid JSON: ' + xhr.responseText);
                            }
                            if (!json || typeof json.location !== 'string') return reject('Invalid response: ' + xhr.responseText);
                            resolve(json.location);
                        };

                        xhr.onerror = function() {
                            reject('Image upload failed.');
                        };

                        const formData = new FormData();
                        formData.append('file', blobInfo.blob(), blobInfo.filename());
                        xhr.send(formData);
                    });
                },
                content_style: 'body{font-family:ui-sans-serif,system-ui,-apple-system,Segoe UI,Roboto,Helvetica Neue,Arial;line-height:1.6;color:#111827;} img{max-width:100%;height:auto;}'
            });
        });
    </script>
</x-app-layout>