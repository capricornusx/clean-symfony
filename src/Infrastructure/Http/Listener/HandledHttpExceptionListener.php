<?php

declare(strict_types=1);

namespace App\Infrastructure\Http\Listener;

use App\Application\Exception\HandledHttpException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Throwable;

readonly class HandledHttpExceptionListener
{
    public function __construct(private NormalizerInterface $normalizer)
    {
    }

    public function handle(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();

        if (!$exception instanceof HandledHttpException) {
            return;
        }

        $response = new JsonResponse();
        $response->headers->set('X-Response-Type', 'FAILURE');

        $response->setStatusCode($exception->getStatusCode());

        try {
            $normalized = $this->normalizer->normalize($exception);
        } catch (Throwable) {
            $normalized = [];
        }

        $response->setData($normalized);

        $event->setResponse($response);
        $event->allowCustomResponseCode();
    }
}
