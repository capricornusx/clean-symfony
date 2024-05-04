<?php

declare(strict_types=1);

namespace App\Infrastructure\Validation;

class InvalidInputOutput
{
    /**
     * Позиция невалидного объекта.
     */
    public int $position;

    /**
     * Свойства объекта которые не прошли валидацию.
     *
     * @var InvalidPropertyOutput[]
     */
    public array $properties;

    /**
     * @param int $position
     * @param InvalidPropertyOutput[] $properties
     */
    public function __construct(int $position, array $properties)
    {
        $this->position = $position;
        $this->properties = $properties;
    }
}
