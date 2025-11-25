<?php

namespace App\Http\Controllers;

use App\Models\Shortlink;
use Illuminate\Http\Request;

class RedirectController extends Controller
{
    public function prepareRedirect($slug, Request $request)
    {
        // Cari shortlink
        $link = Shortlink::where('slug', $slug)->first();

        if (! $link) {
            return response()->json([
                'status' => 'error',
                'message' => 'Link tidak ditemukan'
            ], 404);
        }

        // Cek status link
        if (! $link->is_active) {
            return response()->json([
                'status' => 'inactive',
                'message' => 'Link sudah dinonaktifkan'
            ], 403);
        }

        // Cek domain setting
        $domain = $link->domain;

        if (! $domain->is_active) {
            return response()->json([
                'status' => 'domain_inactive',
                'message' => "Domain {$domain->domain} sedang tidak aktif"
            ], 403);
        }

        if ($domain->is_blocked) {
            return response()->json([
                'status' => 'blocked',
                'message' => "Domain diblokir",
                'reason' => $domain->blocked_reason
            ], 403);
        }

        // Update stats
        $link->increment('hit_count');
        $link->update(['last_hit_at' => now()]);

        // Lakukan redirect
        return redirect("/r/{$slug}");
    }

    public function trackingPage($slug)
    {
        $link = Shortlink::where('slug', $slug)->firstOrFail();

        return view('redir', [
            'slug' => $slug,
            'target' => $link->target_url
        ]);
    }
}
