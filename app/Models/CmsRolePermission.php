<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CmsRolePermission extends Model
{
    protected $fillable = ['role_id', 'category_id', 'action', 'is_allowed'];

    public function role(): BelongsTo
    {
        return $this->belongsTo(CmsRole::class, 'role_id', 'id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(CmsCategory::class, 'category_id');
    }
}
