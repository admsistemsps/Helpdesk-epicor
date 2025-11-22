<?php


namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;


class KbFeedback extends Model
{
    use SoftDeletes;

    protected $dates = ['deleted_at'];
    protected $table = 'kb_feedback';
    protected $fillable = ['article_id', 'user_id', 'was_helpful', 'comment', 'user_agent', 'ip'];


    public function article(): BelongsTo
    {
        return $this->belongsTo(KbArticle::class, 'article_id');
    }


    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
