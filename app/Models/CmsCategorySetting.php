<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CmsCategorySetting extends Model
{
    protected $fillable = ['category_id', 'setting_key', 'setting_value'];

    public function category(): BelongsTo
    {
        return $this->belongsTo(CmsCategory::class, 'category_id');
    }
}
