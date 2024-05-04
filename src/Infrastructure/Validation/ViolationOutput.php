<?php

declare(strict_types=1);

namespace App\Infrastructure\Validation;

use ReflectionObject;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintViolation;

class ViolationOutput
{
    /**
     * @param string $constraint Константа типа ошибки.
     * @param string $message Human readable сообщение предназначенное для frontend разработчиков.
     * Не следует использовать данный текст для отображения конечному пользователю.
     */
    public function __construct(public string $constraint, public string $message, public mixed $invalidValue)
    {
    }

    public static function from(ConstraintViolation $violation): self
    {
        $constraint = self::shortifyConstraint($violation->getConstraint());
        $message = (string)$violation->getMessage();

        return new self(
            $constraint,
            $message,
            $violation->getInvalidValue()
        );
    }

    private static function shortifyConstraint(?Constraint $constraint): string
    {
        if ($constraint === null) {
            return '';
        }

        return (new ReflectionObject($constraint))->getShortName();
    }
}
