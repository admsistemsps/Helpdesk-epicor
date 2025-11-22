<?php


namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;


class KbArticle extends Model
{
    use SoftDeletes;


    protected $table = 'kb_articles';
    protected $fillable = [
        'category_id',
        'owner_id',
        'title',
        'slug',
        'summary',
        'content',
        'status',
        'tags',
        'is_pinned',
        'view_count'
    ];
    protected $casts = [
        'is_published' => 'boolean',
        'published_at' => 'datetime',
        'tags' => 'array',
        'is_pinned' => 'boolean',
        'view_count' => 'integer',
    ];


    protected static function booted(): void
    {
        // Auto slug saat create/update bila slug kosong
        static::saving(function (KbArticle $article) {
            if (empty($article->slug)) {
                $article->slug = Str::slug(Str::limit($article->title, 80, ''));
            }
        });
    }


    public function category(): BelongsTo
    {
        return $this->belongsTo(KbCategory::class, 'category_id');
    }


    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }


    public function feedback(): HasMany
    {
        return $this->hasMany(KbFeedback::class, 'article_id');
    }


    public function scopePublished($q)
    {
        return $q->where('status', 'published');
    }


    public function scopeSearch($q, ?string $term)
    {
        if (!$term) return $q;
        $t = trim($term);
        return $q->where(function ($qq) use ($t) {
            $qq->where('title', 'like', "%{$t}%")
                ->orWhere('summary', 'like', "%{$t}%")
                ->orWhere('content', 'like', "%{$t}%");
        });
    }

    // Route model binding by slug
    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function attachments()
    {
        return $this->hasMany(\App\Models\KbAttachment::class, 'article_id');
    }
}
