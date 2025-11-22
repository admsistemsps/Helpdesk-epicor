<?php

namespace App\Http\Controllers;

use App\Models\KbArticle;
use App\Models\KbCategory;
use App\Models\KbFeedback;
use App\Models\KbAttachment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class KnowledgeBaseController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string) $request->query('q'));

        // Jika ada query search, tampilkan hasil pencarian GLOBAL (semua kategori)
        if ($q !== '') {
            $articles = KbArticle::query()
                ->when($q, fn($qq) => $qq->search($q))
                ->with('category')
                ->orderByDesc('created_at')
                ->paginate(12)
                ->withQueryString();

            return view('kb.search', compact('articles', 'q'));
        }

        // Jika tidak ada search, tampilkan daftar kategori
        $categories = KbCategory::query()
            ->active()
            ->withCount('articles')
            ->orderBy('sort_order')
            ->get();

        return view('kb.index', compact('categories'));
    }

    public function category(Request $request, KbCategory $category)
    {
        $q = trim((string) $request->query('q'));

        // Query artikel dalam kategori ini
        $articles = KbArticle::query()
            ->where('category_id', $category->id)
            ->when($q, fn($qq) => $qq->search($q)) // Search HANYA dalam kategori ini
            ->orderByDesc('created_at')
            ->paginate(12)
            ->withQueryString();

        return view('kb.category', compact('category', 'articles', 'q'));
    }

    public function show(KbCategory $category, KbArticle $article)
    {
        abort_unless($article->category_id === $category->id, 404);

        // Tambah views (tanpa mengubah updated_at)
        if (Schema::hasColumn('kb_articles', 'views')) {
            \App\Models\KbArticle::whereKey($article->id)->increment('views');
        }

        $article->load('attachments');

        return view('kb.article', compact('category', 'article'));
    }

    public function feedback(Request $request, KbCategory $category, KbArticle $article)
    {
        abort_unless($article->category_id === $category->id, 404);

        $data = $request->validate([
            'helpful' => ['required', 'in:0,1'],
            'comment' => ['nullable', 'string', 'max:500'],
        ]);

        KbFeedback::create([
            'article_id' => $article->id,
            'user_id'    => optional($request->user())->id,
            'was_helpful' => (bool)$data['helpful'],
            'comment'    => $data['comment'] ?? null,
            'user_agent' => substr((string) $request->userAgent(), 0, 255),
            'ip'         => $request->ip(),
        ]);

        return back()->with('status', 'Terima kasih atas feedbacknya!');
    }

    // =========================
    // ADMIN (super-admin only)
    // =========================

    public function create(KbCategory $category)
    {
        $article = new KbArticle();
        return view('kb.admin.form', [
            'mode'     => 'create',
            'category' => $category,
            'article'  => $article,
        ]);
    }

    public function store(Request $request, KbCategory $category)
    {
        $this->assertSuperAdmin($request);

        $data = $request->validate([
            'title'        => ['required', 'string', 'max:200'],
            'summary'      => ['nullable', 'string', 'max:300'],
            'content'      => ['required', 'string'], // HTML dari CKEditor
            // Lampiran dokumen (PDF/DOC/DOCX)
            'attachments.*' => ['nullable', 'file', 'max:30720', 'mimetypes:application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document'],
        ]);

        $slug = Str::slug($data['title']);
        if (KbArticle::where('slug', $slug)->exists()) {
            $slug .= '-' . Str::random(6);
        }

        $article = KbArticle::create([
            'category_id'  => $category->id,
            'title'        => $data['title'],
            'slug'         => $slug,
            'summary'      => $data['summary'] ?? null,
            'content'      => $data['content'], // simpan HTML apa adanya
            'views'        => 0,
        ]);

        // Simpan lampiran (pdf/doc/docx)
        $this->storeAttachments($request, $article);

        return redirect()->route('kb.article', [$category, $article])
            ->with('status', 'Artikel berhasil dibuat.');
    }

    public function edit(KbCategory $category, KbArticle $article)
    {
        abort_unless($article->category_id === $category->id, 404);
        $article->load('attachments');

        return view('kb.admin.form', [
            'mode'     => 'edit',
            'category' => $category,
            'article'  => $article,
        ]);
    }

    public function update(Request $request, KbCategory $category, KbArticle $article)
    {
        $this->assertSuperAdmin($request);
        abort_unless($article->category_id === $category->id, 404);

        $data = $request->validate([
            'title'        => ['required', 'string', 'max:200'],
            'summary'      => ['nullable', 'string', 'max:300'],
            'content'      => ['required', 'string'],
            'attachments.*' => ['nullable', 'file', 'max:30720', 'mimetypes:application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document'],
            'delete_attachment_ids' => ['array'],
            'delete_attachment_ids.*' => ['integer'],
        ]);

        // Update slug jika judul berubah
        if ($article->title !== $data['title']) {
            $newSlug = Str::slug($data['title']);
            if (KbArticle::where('slug', $newSlug)->where('id', '!=', $article->id)->exists()) {
                $newSlug .= '-' . Str::random(6);
            }
            $article->slug = $newSlug;
        }

        $article->title   = $data['title'];
        $article->summary = $data['summary'] ?? null;
        $article->content = $data['content'];
        $article->save();

        // Hapus lampiran yang diceklis
        $idsToDelete = (array) ($data['delete_attachment_ids'] ?? []);
        if ($idsToDelete) {
            $attachments = KbAttachment::where('article_id', $article->id)
                ->whereIn('id', $idsToDelete)->get();
            foreach ($attachments as $att) {
                Storage::disk('public')->delete($att->path);
                $att->delete();
            }
        }

        // Tambah lampiran baru
        $this->storeAttachments($request, $article);

        return redirect()->route('kb.article', [$category, $article])
            ->with('status', 'Perubahan tersimpan.');
    }

    public function destroy(KbCategory $category, KbArticle $article)
    {
        $this->assertSuperAdmin(request());
        abort_unless($article->category_id === $category->id, 404);
        // Hapus file lampiran di storage
        foreach ($article->attachments as $att) {
            Storage::disk('public')->delete($att->path);
        }
        $article->delete();

        return redirect()->route('kb.category', $category)->with('status', 'Artikel dihapus.');
    }

    /**
     * Endpoint upload gambar untuk CKEditor 5 SimpleUploadAdapter
     * - hanya super-admin
     * - simpan ke storage/app/public/kb/images
     * - response: { url: "https://..." }
     */
    public function uploadImage(Request $request)
    {
        $this->assertSuperAdmin($request);

        $request->validate([
            'upload' => ['required', 'file', 'image', 'max:10240'], // 10MB
        ]);

        $path = $request->file('upload')->store('kb/images', 'public');
        return response()->json(['url' => asset('storage/' . $path)]);
    }

    /** Simpan banyak lampiran (PDF/DOC/DOCX) */
    private function storeAttachments(Request $request, KbArticle $article): void
    {
        if (!$request->hasFile('attachments')) return;

        foreach ((array) $request->file('attachments') as $file) {
            if (!$file) continue;
            $stored = $file->store('kb/attachments', 'public');

            KbAttachment::create([
                'article_id'    => $article->id,
                'path'          => $stored,
                'original_name' => $file->getClientOriginalName(),
                'mime'          => $file->getClientMimeType(),
                'size'          => $file->getSize(),
            ]);
        }
    }

    /**
     * (BARU) Upload gambar dari TinyMCE.
     * TinyMCE mengirim field 'file', response wajib JSON: { location: "https://..." }.
     */
    public function tinymceUpload(Request $request)
    {
        // pastikan hanya super admin yang boleh upload
        $this->assertSuperAdmin($request);

        // validasi: hanya gambar, maks 10MB
        $request->validate([
            'file' => ['required', 'image', 'max:10240'],
        ]);

        // simpan ke storage/app/public/kb/images
        $path = $request->file('file')->store('kb/images', 'public');

        // kembalikan URL publik ke TinyMCE
        return response()->json(['location' => asset('storage/' . $path)]);
    }


    /** Pastikan user super-admin (tanpa bergantung middleware eksternal) */
    private function assertSuperAdmin(Request $request): void
    {
        $user = $request->user();

        // Pastikan user login dan role_id-nya termasuk 1 atau 2
        $ok = $user && in_array($user->role_id, [1, 2]);

        abort_unless($ok, 403, 'Anda tidak berhak melakukan aksi ini.');
    }
}
