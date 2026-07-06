<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CmsCategory extends Model
{
    protected $fillable = ['parent_id', 'name', 'slug', 'type', 'description', 'is_active', 'sort_order', 'level'];

    protected static function booted()
    {
        static::saving(function ($category) {
            if ($category->parent_id) {
                $parent = CmsCategory::find($category->parent_id);
                $category->level = $parent ? $parent->level + 1 : 1;
            } else {
                $category->level = 1;
            }
        });
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(CmsCategory::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(CmsCategory::class, 'parent_id');
    }

    public function settings(): HasMany
    {
        return $this->hasMany(CmsCategorySetting::class, 'category_id');
    }
}
