<?php

declare(strict_types=1);

namespace App\Infrastructure\Validation;

class InvalidPropertyOutput
{
    private const NON_PROPERTY = '__NON_PROPERTY_ERRORS';

    /**
     * Наименование свойства которое не прошло валидацию.
     */
    public string $property;

    /**
     * Коллекция ошибок свойства.
     *
     * @var ViolationOutput[]
     */
    public array $violations;

    /**
     * @param string $property
     * @param ViolationOutput[] $violations
     */
    public function __construct(string $property, array $violations)
    {
        $this->property = !empty($property) ? $property : self::NON_PROPERTY;
        $this->violations = $violations;
    }
}
