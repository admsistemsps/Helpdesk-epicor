<?php


namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;


class KbCategory extends Model
{
    use SoftDeletes;

    protected $dates = ['deleted_at'];
    protected $table = 'kb_categories';
    protected $fillable = ['name', 'slug', 'description', 'sort_order', 'is_active'];


    public function articles(): HasMany
    {
        return $this->hasMany(KbArticle::class, 'category_id');
    }


    // Route model binding by slug
    public function getRouteKeyName(): string
    {
        return 'slug';
    }


    public function scopeActive($q)
    {
        return $q->where('is_active', true);
    }
}
