<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VisitLog extends Model
{
    protected $fillable = [
        'fingerprint_id',
        'ip_address',
        'visited_at',
    ];

    public function fingerprintDevice()
    {
        return $this->belongsTo(DeviceFingerprint::class, 'fingerprint_id', 'fingerprint_id');
    }
}
