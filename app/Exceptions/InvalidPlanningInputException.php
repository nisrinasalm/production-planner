<?php

namespace App\Exceptions;

use InvalidArgumentException;

class InvalidPlanningInputException extends InvalidArgumentException
{
    public static function negativeOrFraction(int $slotIndex, mixed $value): self
    {
        return new self("Slot index {$slotIndex} bernilai tidak valid: " . var_export($value, true) . ". Nilai harus berupa integer non-negatif.");
    }
 
    public static function empty(): self
    {
        return new self('Collection slot tidak boleh kosong.');
    }
}
 