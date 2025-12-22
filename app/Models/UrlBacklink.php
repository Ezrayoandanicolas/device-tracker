<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UrlBacklink extends Model
{
    protected $fillable = ['url', 'is_active'];

    public function articles()
    {
        return $this->hasMany(BacklinkArticle::class, 'url_backlink_id');
    }
}
