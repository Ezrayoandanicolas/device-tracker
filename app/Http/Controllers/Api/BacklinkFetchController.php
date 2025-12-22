<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\UrlBacklink;
use App\Models\BacklinkArticle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BacklinkFetchController extends Controller
{
    /**
     * Normalize domain:
     * - buang protocol
     * - buang path
     * - buang semua subdomain (ambil root domain)
     */
    private function normalizeDomain(string $domain): string
    {
        $domain = strtolower(trim($domain));

        // buang protocol
        $domain = preg_replace('#^https?://#', '', $domain);

        // buang path & query
        $domain = explode('/', $domain)[0];

        // buang trailing dot
        $domain = rtrim($domain, '.');

        return $domain;
    }


    /**
     * POST /backlink/claim
     * Generate + claim backlink (1x per artikel)
     */
    public function claim(Request $request)
    {
        // dd('CLAIM METHOD HIT');
        \Log::info('=== BACKLINK CLAIM HIT ===');
        \Log::info('RAW REQUEST', $request->all());
        
        try {
            return 'claim ok';
            $request->validate([
                'article_slug'   => 'required|string',
                'article_domain' => 'required|string',
                'limit'          => 'required|integer|min:1|max:5',
            ]);
        } catch (\Throwable $e) {
            \Log::error('VALIDATION FAILED', [
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }

        $slug   = $request->article_slug;
        $domain = $this->normalizeDomain($request->article_domain);
        $limit  = (int) $request->limit;

        \Log::info('NORMALIZED INPUT', [
            'slug'   => $slug,
            'domain' => $domain,
            'limit'  => $limit,
        ]);

        return DB::transaction(function () use ($slug, $domain, $limit) {

            \Log::info('DB TRANSACTION START');

            // 1️⃣ existing
            $existing = BacklinkArticle::with('backlink')
                ->where('article_slug', $slug)
                ->where('article_domain', $domain)
                ->get();

            \Log::info('EXISTING FOUND', [
                'count' => $existing->count(),
                'rows'  => $existing->toArray(),
            ]);

            if ($existing->count() >= $limit) {
                \Log::info('EXISTING >= LIMIT, RETURNING');

                return response()->json([
                    'data' => $existing->map(fn ($i) => [
                        'url' => optional($i->backlink)->url
                            ? rtrim($i->backlink->url, '/') . '/?page=' . $slug
                            : null,
                    ])->filter()->values(),
                ]);
            }

            $needed = $limit - $existing->count();

            \Log::info('NEEDED BACKLINK', [
                'needed' => $needed,
            ]);

            // 2️⃣ ambil backlink
            $available = UrlBacklink::where('is_active', true)
                ->inRandomOrder()
                ->limit($needed)
                ->get();

            \Log::info('AVAILABLE BACKLINKS', [
                'count' => $available->count(),
                'rows'  => $available->toArray(),
            ]);

            // 3️⃣ insert
            foreach ($available as $b) {
                \Log::info('INSERTING BACKLINK', [
                    'url_backlink_id' => $b->id,
                    'slug' => $slug,
                    'domain' => $domain,
                ]);

                BacklinkArticle::create([
                    'url_backlink_id' => $b->id,
                    'article_slug'    => $slug,
                    'article_domain'  => $domain,
                ]);
            }

            // 4️⃣ final fetch
            $final = BacklinkArticle::with('backlink')
                ->where('article_slug', $slug)
                ->where('article_domain', $domain)
                ->get();

            \Log::info('FINAL RESULT', [
                'count' => $final->count(),
                'rows'  => $final->toArray(),
            ]);

            return response()->json([
                'data' => $final->map(fn ($i) => [
                    'url' => optional($i->backlink)->url
                        ? rtrim($i->backlink->url, '/') . '/?page=' . $slug
                        : null,
                ])->filter()->values(),
            ]);
        });
    }


    /**
     * GET /backlink/get
     * Read-only, aman dipanggil berkali-kali
     */
    public function get(Request $request)
    {
        $request->validate([
            'article_slug'   => 'required|string',
            'article_domain' => 'required|string',
        ]);

        $slug   = $request->article_slug;
        $domain = $this->normalizeDomain($request->article_domain);

        // 🔥 ambil backlink aktif
        $rows = BacklinkArticle::query()
            ->join('url_backlinks', 'url_backlinks.id', '=', 'backlink_articles.url_backlink_id')
            ->where('backlink_articles.article_slug', $slug)
            ->where('backlink_articles.article_domain', $domain)
            ->where('url_backlinks.is_active', true)
            ->select(
                'backlink_articles.id',
                'url_backlinks.url'
            )
            ->get();

        // 🔥 TRACKING: increment views
        if ($rows->isNotEmpty()) {
            BacklinkArticle::whereIn(
                'id',
                $rows->pluck('id')
            )->increment('views');
        }

        $data = $rows->map(fn ($row) => [
            'url' => rtrim($row->url, '/') . '/?page=' . $slug,
        ])->values();

        return response()->json(['data' => $data]);
    }


    public function track(Request $request)
    {
        $request->validate([
            'article_slug'   => 'required|string',
            'article_domain' => 'required|string',
            'url'            => 'required|url',
        ]);

        BacklinkArticle::where('article_slug', $request->article_slug)
            ->where('article_domain', $this->normalizeDomain($request->article_domain))
            ->whereHas('backlink', fn ($q) =>
                $q->where('url', $request->url)
            )
            ->increment('views');

        return response()->json(['status' => 'ok']);
    }

    public function index()
    {
        $data = UrlBacklink::withCount('articles')->get();

        return response()->json([
            'status' => true,
            'data' => $data,
        ]);
    }


    public function store(Request $request)
    {
        // ✅ Validasi
        $validated = $request->validate([
            'url' => 'required|url|max:255|unique:url_backlinks,url',
        ]);

        // ✅ Simpan
        $backlink = UrlBacklink::create([
            'url' => $validated['url'],
            'is_active' => true,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'URL backlink berhasil ditambahkan',
            'data' => $backlink,
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $backlink = UrlBacklink::findOrFail($id);

        // ✅ Validasi
        $validated = $request->validate([
            'url' => 'required|url|max:255|unique:url_backlinks,url,' . $backlink->id,
            'is_active' => 'required|boolean',
        ]);

        // ✅ Update data
        $backlink->update($validated);

        return response()->json([
            'status' => true,
            'message' => 'URL backlink berhasil diupdate',
            'data' => $backlink,
        ]);
    }

    public function usageCount()
    {
        $total = BacklinkArticle::whereNotNull('url_backlink_id')->count();

        return response()->json([
            'status' => true,
            'total_usage' => $total,
        ]);
    }

    public function usageCountById($id)
    {
        $total = BacklinkArticle::where('url_backlink_id', $id)->count();

        return response()->json([
            'status' => true,
            'url_backlink_id' => (int) $id,
            'total_usage' => $total,
        ]);
    }


}
