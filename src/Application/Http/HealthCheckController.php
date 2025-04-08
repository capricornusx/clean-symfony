<?php

declare(strict_types=1);

namespace App\Application\Http;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HealthCheckController extends AbstractController
{
    public const ROUTE = '/health-check';

    #[Route(path: self::ROUTE, name: self::class, methods: ['GET'])]
    public function healthAction(): Response
    {
        /**
         * можно добавить promphp/prometheus_client_php
         * и отдавать кастомные метрики в Prometheus
         */
        return new Response(content: 'ok', status: 200);
    }
}
