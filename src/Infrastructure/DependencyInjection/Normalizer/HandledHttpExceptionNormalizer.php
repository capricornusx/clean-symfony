<?php

declare(strict_types=1);

namespace App\Infrastructure\DependencyInjection\Normalizer;

use App\Application\Exception\HandledHttpException;
use App\Application\Exception\ValidationException;
use App\Infrastructure\Validation\InputValidationFormatter;
use Symfony\Component\Serializer\Normalizer\CacheableSupportsMethodInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

final class HandledHttpExceptionNormalizer implements NormalizerInterface, CacheableSupportsMethodInterface
{
    public function supportsNormalization($data, string $format = null): bool
    {
        return $data instanceof HandledHttpException;
    }

    public function hasCacheableSupportsMethod(): bool
    {
        return true;
    }

    public function normalize($object, string $format = null, array $context = []): array
    {
        assert($object instanceof HandledHttpException);

        $message = match(true) {
            $object instanceof ValidationException => $this->normalizeValidationException($object),
            default => $object->getMessage()
        };

        return [
            'slug' => $object->getSlug(),
            'message' => $message
        ];
    }

    private function normalizeValidationException(ValidationException $exception): array
    {
        return InputValidationFormatter::format($exception->getViolations());
    }
}
