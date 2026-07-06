<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Tenant extends Model
{
    use HasUuids;

    protected $fillable = [
        'name',
        'domain',
        'database_name',
        'status',
    ];
}
