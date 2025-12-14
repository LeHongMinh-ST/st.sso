<?php

declare(strict_types=1);

namespace App\SharedKernel\Infrastructure\Http\Controllers;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;

/**
 * Health check controller for monitoring application status.
 */
final class HealthCheckController
{
    /**
     * Basic health check endpoint.
     *
     * @return JsonResponse
     */
    public function basic(): JsonResponse
    {
        return response()->json([
            'status' => 'ok',
            'timestamp' => now()->toIso8601String(),
            'version' => config('app.version', '1.0.0'),
        ]);
    }

    /**
     * Detailed health check endpoint with system checks.
     *
     * @return JsonResponse
     */
    public function detailed(): JsonResponse
    {
        $checks = [
            'database' => $this->checkDatabase(),
            'cache' => $this->checkCache(),
        ];

        $allHealthy = collect($checks)->every(fn ($check) => 'ok' === $check['status']);

        return response()->json([
            'status' => $allHealthy ? 'ok' : 'degraded',
            'timestamp' => now()->toIso8601String(),
            'version' => config('app.version', '1.0.0'),
            'checks' => $checks,
        ], $allHealthy ? 200 : 503);
    }

    /**
     * Check database connectivity.
     *
     * @return array<string, mixed>
     */
    private function checkDatabase(): array
    {
        try {
            DB::connection()->getPdo();
            return [
                'status' => 'ok',
                'message' => 'Database connection successful',
            ];
        } catch (Exception $e) {
            return [
                'status' => 'error',
                'message' => 'Database connection failed: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Check cache connectivity.
     *
     * @return array<string, mixed>
     */
    private function checkCache(): array
    {
        try {
            $driver = config('cache.default');
            if ('redis' === $driver) {
                Redis::connection()->ping();
            } else {
                cache()->put('health_check', 'ok', 1);
                cache()->get('health_check');
            }
            return [
                'status' => 'ok',
                'message' => 'Cache connection successful',
                'driver' => $driver,
            ];
        } catch (Exception $e) {
            return [
                'status' => 'error',
                'message' => 'Cache connection failed: ' . $e->getMessage(),
            ];
        }
    }
}
