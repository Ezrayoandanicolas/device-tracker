<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DomainList extends Model
{
    protected $fillable = [
        'domain',
        'is_active',
        'is_blocked',
        'blocked_reason',
    ];
}
