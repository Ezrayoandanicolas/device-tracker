<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DeviceFingerprint;
use App\Models\VisitLog;

class DeviceFingerprintController extends Controller
{
    public function generateOrUpdateFingerprint(Request $request)
    {
        $request->validate([
            'fingerprint_id' => 'required|string',
            'device_hash' => 'required|string',
            'browser_hash' => 'required|string',
            'os_hash' => 'required|string',
            'ip_address' => 'nullable|string'
        ]);

        $device = DeviceFingerprint::where('fingerprint_id', $request->fingerprint_id)->first();

        $fingerprintJson = json_encode([
            'device_hash' => $request->device_hash,
            'browser_hash' => $request->browser_hash,
            'os_hash' => $request->os_hash,
        ]);

        if (!$device) {

            // Device baru
            $device = DeviceFingerprint::create([
                'fingerprint_id' => $request->fingerprint_id,
                'ip_address' => $request->ip_address,   // IP pertama saja
                'fingerprint_data' => $fingerprintJson,
                'scan_count' => 1,
                'similarity_score' => 1.000,
            ]);

        } else {

            // Device lama → JANGAN update ip_address
            $device->update([
                'fingerprint_data' => $fingerprintJson,
                'scan_count' => $device->scan_count + 1,
            ]);
        }

        // SELALU BUAT LOG BARU
        VisitLog::create([
            'fingerprint_id' => $request->fingerprint_id,
            'ip_address' => $request->ip_address,
            'visited_at' => now(),
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Fingerprint stored successfully',
            'data' => $device
        ]);
    }



    // Search device by IP
    public function getByIp(Request $request)
    {
        $request->validate([
            'ip' => 'required|string'
        ]);

        // Ambil semua log berdasarkan IP
        $logs = VisitLog::with('fingerprintDevice')
            ->where('ip_address', $request->ip)
            ->orderBy('visited_at', 'desc')
            ->get();

        // Ambil device unik dari log
        $uniqueDevices = $logs->pluck('fingerprintDevice')->unique('fingerprint_id')->values();

        return response()->json([
            'status' => 'success',
            'ip_searched' => $request->ip,
            'message' => count($uniqueDevices) > 0
                ? "IP ini pernah digunakan oleh " . count($uniqueDevices) . " device"
                : "IP ini belum pernah muncul dalam sistem",

            // list device unik
            'devices_using_ip' => $uniqueDevices->map(function ($device) {
                return [
                    'fingerprint_id' => $device->fingerprint_id,
                    'ip_address' => $device->ip_address,
                    'scan_count' => $device->scan_count,
                    'similarity_score' => $device->similarity_score,
                    'first_seen_at' => $device->created_at,
                    'last_seen_at' => $device->updated_at,
                ];
            }),

            // seluruh riwayat visit log (list penuh)
            'visit_logs' => $logs->map(function ($log) {
                return [
                    'ip' => $log->ip_address,
                    'fingerprint_id' => $log->fingerprint_id,
                    'visited_at' => $log->visited_at,
                ];
            }),
        ]);
    }

    public function getAllIpGroups()
    {
        // Ambil semua visit logs
        $logs = VisitLog::with('fingerprintDevice')
            ->orderBy('ip_address')
            ->orderBy('visited_at', 'desc')
            ->get();

        // Grup berdasarkan IP
        $grouped = $logs->groupBy('ip_address');

        // Format data
        $result = $grouped->map(function ($logGroup, $ip) {

            // Ambil device unik untuk IP tersebut
            $uniqueDevices = $logGroup->pluck('fingerprintDevice')
                ->unique('fingerprint_id')
                ->values();

            return [
                'ip' => $ip,
                'total_devices' => $uniqueDevices->count(),
                'devices' => $uniqueDevices->map(function ($d) {
                    return [
                        'fingerprint_id' => $d->fingerprint_id,
                        'scan_count' => $d->scan_count,
                        'similarity_score' => $d->similarity_score,
                        'first_seen_at' => $d->created_at,
                        'last_seen_at' => $d->updated_at,
                    ];
                }),

                // tampilin semua visit log IP itu
                'visit_logs' => $logGroup->map(function ($log) {
                    return [
                        'fingerprint_id' => $log->fingerprint_id,
                        'visited_at' => $log->visited_at,
                    ];
                }),
            ];
        })->values();

        return response()->json([
            'status' => 'success',
            'total_ips' => $result->count(),
            'ip_groups' => $result
        ]);
    }

    public function getDevicesWithIps()
    {
        // Ambil semua visit logs + relasi device
        $logs = VisitLog::with('fingerprintDevice')
            ->orderBy('fingerprint_id')
            ->orderBy('visited_at', 'desc')
            ->get();

        // Group berdasarkan DEVICE
        $devices = $logs->groupBy('fingerprint_id');

        $result = $devices->map(function ($deviceLogs, $fingerprintId) {

            // Ambil device info (satu contoh dari log)
            $device = $deviceLogs->first()->fingerprintDevice;

            // Ambil list IP unik + jumlah kunjungan per IP
            $ipGroups = $deviceLogs
                ->groupBy('ip_address')
                ->map(function ($ipLog, $ip) {
                    return [
                        'ip' => $ip,
                        'visit_count' => $ipLog->count(),
                        'first_seen' => $ipLog->min('visited_at'),
                        'last_seen' => $ipLog->max('visited_at'),
                    ];
                })
                ->values();

            return [
                'fingerprint_id' => $fingerprintId,
                'total_ips_used' => $ipGroups->count(),
                'device_summary' => 
                    $ipGroups->count() === 1
                    ? "Device ini hanya menggunakan 1 IP"
                    : "Device ini terdeteksi menggunakan {$ipGroups->count()} IP yang berbeda",

                'ips' => $ipGroups,
                'scan_count' => $device?->scan_count,
                'similarity_score' => $device?->similarity_score,
                'first_seen_at' => $device?->created_at,
                'last_seen_at' => $device?->updated_at,
            ];
        })->values();

        return response()->json([
            'status' => 'success',
            'total_devices' => $result->count(),
            'data' => $result,
        ]);
    }

    public function getDevicesBySpecificIp(Request $request)
    {
        $request->validate([
            'ip' => 'required|string'
        ]);

        $targetIp = $request->ip;

        // Ambil semua log yg mengandung IP target + relasi device
        $logsForIp = VisitLog::with('fingerprintDevice')
            ->where('ip_address', $targetIp)
            ->orderBy('fingerprint_id')
            ->orderBy('visited_at', 'desc')
            ->get();

        // GROUP berdasarkan device fingerprint_id
        $devices = $logsForIp->groupBy('fingerprint_id');

        $result = $devices->map(function ($deviceLogs, $fingerprintId) use ($targetIp) {

            $device = $deviceLogs->first()->fingerprintDevice;

            // Ambil semua IP lain yg pernah dipakai device ini
            $allLogs = VisitLog::where('fingerprint_id', $fingerprintId)->get();

            $ipGroups = $allLogs->groupBy('ip_address')
                ->map(function ($ipLog, $ip) {
                    return [
                        'ip' => $ip,
                        'visit_count' => $ipLog->count(),
                        'first_seen' => $ipLog->min('visited_at'),
                        'last_seen' => $ipLog->max('visited_at'),
                    ];
                })
                ->values();

            return [
                'fingerprint_id' => $fingerprintId,
                'ip_target_matched' => $targetIp,
                'total_ips_used' => $ipGroups->count(),
                'device_summary' =>
                    $ipGroups->count() === 1
                    ? "Device ini hanya memakai 1 IP (termasuk IP yang dicari)"
                    : "Device ini memakai {$ipGroups->count()} IP berbeda (termasuk IP yang dicari)",

                'ips' => $ipGroups,

                'scan_count' => $device?->scan_count,
                'similarity_score' => $device?->similarity_score,
                'first_seen_at' => $device?->created_at,
                'last_seen_at' => $device?->updated_at,
            ];
        })->values();

        return response()->json([
            'status' => 'success',
            'searched_ip' => $targetIp,
            'total_devices_found' => $result->count(),
            'data' => $result,
        ]);
    }


}
