<?php

namespace Core\Domain\Entity\Validation;

use Core\Domain\Entity\Exception\EntityValidationException;

class DomainValidation
{
    public static function notNull(mixed $value, ?string $message = null): void
    {
        if (empty($value)) {
            throw new EntityValidationException($message ?? 'Value must be not empty or null');
        }
    }

    public static function strMaxLength(string $value, int $maxLength = 255, ?string $message = null): void
    {
        if (strlen($value) > $maxLength) {
            throw new EntityValidationException($message ?? 'Value must be less than ' . $maxLength . ' characters');
        }
    }
    public static function strMinLength(string $value, int $minLength = 3, ?string $message = null): void
    {
        if (strlen($value) < $minLength) {
            throw new EntityValidationException($message ?? 'Value must be at least ' . $minLength . ' characters');
        }
    }

    public static function strCanBeNullAndMaxLength(?string $value, int $maxLength = 255, ?string $message = null): void
    {
        if ($value !== null && strlen($value) > $maxLength) {
            throw new EntityValidationException($message ?? 'Value must be less than ' . $maxLength . ' characters');
        }
    }
}
