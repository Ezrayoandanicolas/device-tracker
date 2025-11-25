<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeviceFingerprint extends Model
{
    protected $table = 'device_fingerprints';

    protected $fillable = [
        'fingerprint_id',
        'ip_address',
        'fingerprint_data',
        'scan_count',
        'similarity_score',
    ];

    protected $casts = [
        'fingerprint_data' => 'array',
        'similarity_score' => 'float',
    ];

    /**
     * Update scan count & last seen timestamp
     */
    public function incrementScan()
    {
        $this->scan_count += 1;
        $this->touch(); // update updated_at
        $this->save();
    }

    /**
     * Hitung similarity antara fingerprint baru & fingerprint lama
     */
    public function calculateSimilarity(array $newFp): float
    {
        $old = $this->fingerprint_data;

        $weight = [
            'canvasHash' => 0.20,
            'webglKey' => 0.20,
            'audioHash' => 0.15,
            'fontsHash' => 0.15,
            'screen' => 0.10,
            'cores' => 0.05,
            'deviceMemory' => 0.05,
            'platform' => 0.05,
            'userAgent' => 0.05,
        ];

        $score = 0;

        foreach ($weight as $key => $w) {
            if (!isset($old[$key]) || !isset($newFp[$key])) {
                continue;
            }

            if ($old[$key] === $newFp[$key]) {
                $score += $w;
            }
        }

        return round($score, 3); // contoh output: 0.90
    }

    public function visitLogs()
    {
        return $this->hasMany(VisitLog::class, 'fingerprint_id', 'fingerprint_id');
    }

}
