<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BacklinkArticle extends Model
{
    protected $fillable = [
        'url_backlink_id',
        'article_slug',
        'article_domain',
        'views',
    ];

    // 🔑 INI WAJIB ADA
    public function backlink()
    {
        return $this->belongsTo(UrlBacklink::class, 'url_backlink_id');
    }
}
