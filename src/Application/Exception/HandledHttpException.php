<?php

declare(strict_types=1);

namespace App\Application\Exception;

use RuntimeException;
use Throwable;

abstract class HandledHttpException extends RuntimeException
{
    public function __construct(
        ?string $message = '',
        ?int $statusCode = null,
        Throwable $previous = null,
    ) {
        parent::__construct(
            $message ?? '',
            $statusCode ?? $this->getStatusCode(),
            $previous
        );
    }

    abstract public function getStatusCode(): int;
    abstract public function getSlug(): string;

    final protected static function format(string $message, ...$args): static
    {
        return new static(vsprintf($message, $args));
    }
}
