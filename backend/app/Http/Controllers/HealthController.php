<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class HealthController extends Controller
{
    /**
     * Memeriksa kesehatan sistem (konektivitas Database & Cache).
     */
    public function check(): JsonResponse
    {
        $status = 'healthy';
        $httpCode = 200;
        $checks = [];

        // 1. Pengujian Basis Data Riil (Ping PDO MySQL)
        try {
            DB::connection()->getPdo();
            $checks['database'] = [
                'status' => 'healthy',
                'message' => 'Koneksi ke basis data MySQL berhasil.',
            ];
        } catch (\Throwable $e) {
            $status = 'unhealthy';
            $httpCode = 503;
            $checks['database'] = [
                'status' => 'unhealthy',
                'message' => 'Basis data tidak dapat dihubungi.',
            ];

            // LOG TERSTRUKTUR: Gagal koneksi basis data (ERROR)
            Log::error('Konektivitas basis data terputus pada probe healthcheck', [
                'initiator' => 'health_checker',
                'action'    => 'database_ping',
                'target'    => config('database.connections.mysql.host'),
                'reason'    => $e->getMessage(),
            ]);
        }

        // 2. Pengujian Cache Riil (Write, Read, Forget)
        try {
            $cacheKey = 'health_probe_' . microtime(true);
            Cache::put($cacheKey, 'ok', 10);
            $cacheVal = Cache::get($cacheKey);
            Cache::forget($cacheKey);

            if ($cacheVal !== 'ok') {
                throw new \Exception('Verifikasi nilai cache tidak valid.');
            }

            $checks['cache'] = [
                'status' => 'healthy',
                'driver' => config('cache.default'),
            ];
        } catch (\Throwable $e) {
            $status = 'unhealthy';
            $httpCode = 503;
            $checks['cache'] = [
                'status' => 'unhealthy',
                'message' => $e->getMessage(),
            ];

            // LOG TERSTRUKTUR: Gagal cache (ERROR)
            Log::error('Sistem cache mengalami kegagalan fungsi', [
                'initiator' => 'health_checker',
                'action'    => 'cache_read_write',
                'driver'    => config('cache.default'),
                'reason'    => $e->getMessage(),
            ]);
        }

        return response()->json([
            'status'    => $status,
            'timestamp' => now()->toIso8601String(),
            'checks'    => $checks,
        ], $httpCode);
    }
}
