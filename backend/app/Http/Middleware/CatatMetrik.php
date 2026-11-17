<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Prometheus\CollectorRegistry;
use Symfony\Component\HttpFoundation\Response;

/**
 * Mencatat jumlah dan durasi setiap request HTTP untuk Prometheus.
 *
 * Label route memakai POLA route (mis. "api/items/{item}"), bukan URL asli,
 * supaya jumlah deret waktu tetap kecil (label = kategori, bukan identitas).
 */
class CatatMetrik
{
    public function __construct(private CollectorRegistry $registry) {}

    public function handle(Request $request, Closure $next): Response
    {
        $mulai = microtime(true);

        $response = $next($request);

        $route = $request->route()?->uri() ?? 'tidak_dikenal';

        // Request dari Prometheus sendiri tidak ikut dihitung
        if ($route === 'metrics') {
            return $response;
        }

        $method = $request->method();

        $this->registry
            ->getOrRegisterCounter(
                'app',
                'http_requests_total',
                'Jumlah request HTTP yang dilayani backend',
                ['method', 'route', 'status'],
            )
            ->inc([$method, $route, (string) $response->getStatusCode()]);

        $this->registry
            ->getOrRegisterHistogram(
                'app',
                'http_request_duration_seconds',
                'Lama backend melayani satu request (detik)',
                ['method', 'route'],
                [0.05, 0.1, 0.25, 0.5, 1, 2.5, 5],
            )
            ->observe(microtime(true) - $mulai, [$method, $route]);

        return $response;
    }
}
