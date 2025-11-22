<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class KbAttachment extends Model
{
    use SoftDeletes;

    protected $dates = ['deleted_at'];

    protected $fillable = ['article_id', 'path', 'original_name', 'mime', 'size'];

    public function article()
    {
        return $this->belongsTo(KbArticle::class, 'article_id');
    }

    // URL publik untuk dipakai di Blade
    public function url(): string
    {
        return asset('storage/' . $this->path);
    }

    public function ext(): string
    {
        return strtolower(pathinfo($this->original_name, PATHINFO_EXTENSION));
    }

    public function sizeKB(): string
    {
        return number_format($this->size / 1024, 1) . ' KB';
    }
}
