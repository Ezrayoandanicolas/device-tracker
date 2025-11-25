<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Shortlink extends Model
{
    protected $fillable = [
        'slug',
        'domain_list_id',
        'target_url',
        'is_active',
        'hit_count',
        'last_hit_at',
    ];

    public function domain()
    {
        return $this->belongsTo(DomainList::class, 'domain_list_id');
    }
}
