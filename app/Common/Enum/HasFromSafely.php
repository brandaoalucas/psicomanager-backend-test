<?php

namespace App\Common\Enum;

use Throwable;

trait HasFromSafely
{
    public static function fromSafely(mixed $value): static|null
    {
        try {
            if (is_string($value) || is_int($value)) {
                return static::tryFrom($value);
            }

            return $value;
        } catch (Throwable $e) {
            return null;
        }
    }
}
