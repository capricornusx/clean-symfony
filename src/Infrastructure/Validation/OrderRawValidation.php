<?php

declare(strict_types=1);

namespace App\Infrastructure\Validation;

use Psr\Log\LoggerInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\ConstraintViolationInterface;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validation;

/** Пример валидации */
class OrderRawValidation
{
    public const RAW_ORDER = 'raw_order';

    private int $failures = 0;
    private int $success = 0;

    public function __construct(private readonly LoggerInterface $logger)
    {
    }

    /**
     * Валидация отдельных полей входящего заказа
     */
    public function validate($order): bool
    {
        $validator = Validation::createValidator();
        $groups = new Assert\GroupSequence([self::RAW_ORDER]);

        $constraint = new Assert\Collection([
            'allowExtraFields' => true,
            'fields' => [
                'phone' => new Assert\Required([
                    'constraints' => [
                        new Assert\NotBlank(),
                        new Assert\NotNull(),
                        new Assert\NotEqualTo(' '),
                    ],
                    'groups' => [self::RAW_ORDER],
                ]),
                'status' => new Assert\Required([
                    'constraints' => [
                        new Assert\NotBlank(),
                        new Assert\NotNull(),
                        new Assert\NotEqualTo(' '),
                    ],
                    'groups' => [self::RAW_ORDER],
               ]),
            ],
            'groups' => [self::RAW_ORDER],
        ]);

        $violations = $validator->validate($order, $constraint, $groups);

        if ($violations->count() > 0) {
            $this->failures++;

            $order['errors'] = $this->getDetails($violations);
            $this->logger->error('validation_error', $order);
            return false;
        }

        $this->success++;
        return true;
    }

    public function getFailures(): int
    {
        return $this->failures;
    }

    public function getSuccess(): int
    {
        return $this->success;
    }

    private function getDetails(ConstraintViolationListInterface $violations): array
    {
        $messages = [];

        /** @var ConstraintViolationInterface $violation */
        foreach ($violations as $violation) {
            $messages[] = [
                'property' => str_replace(['[', ']'], ['',''], $violation->getPropertyPath()) ,
                'text' => $violation->getMessage()
            ];
        }

        return $messages;
    }
}
