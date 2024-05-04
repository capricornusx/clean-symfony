<?php

declare(strict_types=1);

namespace App\Infrastructure\Validation;

use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationListInterface;

use function Fp\Collection\map;

class InputValidationFormatter
{
    /**
     * Формирует ответ для невалидных полей.
     *
     * @param ConstraintViolationListInterface $violations
     * @return InvalidPropertyOutput[]
     *     Пустой массив если input валиден. Иначе ошибки валидации свойств.
     */
    public static function format(ConstraintViolationListInterface $violations): array
    {
        $propertyGroups = self::group($violations);
        $invalidProperties = [];

        foreach ($propertyGroups as $property => $propertyGroupViolations) {
            $invalidProperties[] = new InvalidPropertyOutput(
                $property,
                map(
                    $propertyGroupViolations,
                    fn(ConstraintViolation $v) => ViolationOutput::from($v)
                )
            );
        }

        return $invalidProperties;
    }

    /**
     * Группирует нарушения валидации по именам свойств.
     *
     * @param ConstraintViolationListInterface $violations
     * @return ConstraintViolation[][]
     */
    private static function group(ConstraintViolationListInterface $violations): array
    {
        $groups = [];

        /** @var ConstraintViolation $violation */
        foreach ($violations as $violation) {
            $property = $violation->getPropertyPath();

            if (!array_key_exists($property, $groups)) {
                $groups[$property] = [];
            }

            $groups[$property][] = $violation;
        }

        return $groups;
    }
}
