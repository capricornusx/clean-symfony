<?php

declare(strict_types=1);

namespace App\Application\Exception;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\ConstraintViolationInterface;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Throwable;

class ValidationException extends HandledHttpException
{
    public function __construct(
        private ConstraintViolationListInterface $violations,
        Throwable $previous = null
    )
    {
        parent::__construct($this->getMessages(), $this->getStatusCode(), $previous);
    }

    public function getStatusCode(): int
    {
        return Response::HTTP_UNPROCESSABLE_ENTITY;
    }

    public function getSlug(): string
    {
        return 'VALIDATION_ERROR';
    }

    public function getViolations(): ConstraintViolationListInterface
    {
        return $this->violations;
    }

    public function getMessages(): string
    {
        $messages = '';
        /** @var ConstraintViolationInterface $violation */
        foreach ($this->violations as $violation) {
            $messages .= $violation->getMessage();
        }

        return $messages;
    }
}
