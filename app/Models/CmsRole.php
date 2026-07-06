<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CmsRole extends Model
{
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = ['id', 'name', 'description', 'is_system'];

    public function permissions(): HasMany
    {
        return $this->hasMany(CmsRolePermission::class, 'role_id', 'id');
    }
}
