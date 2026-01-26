<?php

namespace App\Controller;

use Doctrine\DBAL\Connection;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Health Check Controller pour le monitoring
 * Permet de vérifier que l'application fonctionne correctement
 */
class HealthController extends AbstractController
{
    #[Route('/health', name: 'health_check', methods: ['GET'])]
    public function index(Connection $connection): JsonResponse
    {
        $status = 'healthy';
        $checks = [];
        $httpStatus = Response::HTTP_OK;

        // Check 1: Application
        $checks['application'] = [
            'status' => 'ok',
            'version' => '1.0.0',
            'environment' => $_ENV['APP_ENV'] ?? 'prod',
        ];

        // Check 2: Database
        try {
            $connection->executeQuery('SELECT 1');
            $checks['database'] = [
                'status' => 'ok',
                'type' => 'mysql',
            ];
        } catch (\Exception $e) {
            $checks['database'] = [
                'status' => 'error',
                'message' => 'Database connection failed',
            ];
            $status = 'unhealthy';
            $httpStatus = Response::HTTP_SERVICE_UNAVAILABLE;
        }

        // Check 3: Cache directory writable
        $cacheDir = $this->getParameter('kernel.cache_dir');
        $checks['cache'] = [
            'status' => is_writable($cacheDir) ? 'ok' : 'error',
        ];
        if ($checks['cache']['status'] === 'error') {
            $status = 'degraded';
        }

        // Check 4: Log directory writable
        $logDir = $this->getParameter('kernel.logs_dir');
        $checks['logs'] = [
            'status' => is_writable($logDir) ? 'ok' : 'error',
        ];
        if ($checks['logs']['status'] === 'error') {
            $status = 'degraded';
        }

        // Check 5: Memory usage
        $memoryUsage = memory_get_usage(true);
        $memoryLimit = $this->getMemoryLimitInBytes();
        $memoryPercent = $memoryLimit > 0 ? round(($memoryUsage / $memoryLimit) * 100, 2) : 0;
        
        $checks['memory'] = [
            'status' => $memoryPercent < 80 ? 'ok' : 'warning',
            'usage_mb' => round($memoryUsage / 1024 / 1024, 2),
            'percent' => $memoryPercent,
        ];

        // Check 6: Disk space
        $uploadDir = $this->getParameter('kernel.project_dir') . '/public/uploads';
        if (is_dir($uploadDir)) {
            $freeSpace = disk_free_space($uploadDir);
            $totalSpace = disk_total_space($uploadDir);
            $usedPercent = round((($totalSpace - $freeSpace) / $totalSpace) * 100, 2);
            
            $checks['disk'] = [
                'status' => $usedPercent < 90 ? 'ok' : 'warning',
                'free_gb' => round($freeSpace / 1024 / 1024 / 1024, 2),
                'used_percent' => $usedPercent,
            ];
        }

        return new JsonResponse([
            'status' => $status,
            'timestamp' => (new \DateTime())->format('c'),
            'checks' => $checks,
        ], $httpStatus);
    }

    /**
     * Endpoint simple pour les load balancers (retourne juste 200 OK)
     */
    #[Route('/health/ping', name: 'health_ping', methods: ['GET'])]
    public function ping(): Response
    {
        return new Response('pong', Response::HTTP_OK, [
            'Content-Type' => 'text/plain',
        ]);
    }

    private function getMemoryLimitInBytes(): int
    {
        $memoryLimit = ini_get('memory_limit');
        
        if ($memoryLimit === '-1') {
            return PHP_INT_MAX;
        }

        $unit = strtolower(substr($memoryLimit, -1));
        $value = (int) substr($memoryLimit, 0, -1);

        return match ($unit) {
            'g' => $value * 1024 * 1024 * 1024,
            'm' => $value * 1024 * 1024,
            'k' => $value * 1024,
            default => (int) $memoryLimit,
        };
    }
}
