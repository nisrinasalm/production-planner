<?php

namespace App\Services;

use App\Exceptions\InvalidPlanningInputException;

class BalancingService
{
    public function balance(array $originalQuantities): array
    {
        $this->validate($originalQuantities);

        $values = array_values($originalQuantities);
        $n = count($values);

        $activeIndexes = [];
        foreach ($values as $i => $qty) {
            if ($qty > 0) {
                $activeIndexes[] = $i;
            }
        }

        $result = array_fill(0, $n, 0);

        if (empty($activeIndexes)) {
            return $result;
        }

        $total = array_sum($values);
        $activeCount = count($activeIndexes);
        $base = intdiv((int) $total, $activeCount);
        $remainder = ((int) $total) % $activeCount;

        $sortedByPriority = $activeIndexes;
        usort($sortedByPriority, function (int $a, int $b) use ($values) {
            if ($values[$a] === $values[$b]) {
                return $a <=> $b;
            }

            return $values[$b] <=> $values[$a];
        });

        $bonusIndexes = array_slice($sortedByPriority, 0, $remainder);
        $bonusSet = array_flip($bonusIndexes);

        foreach ($activeIndexes as $i) {
            $result[$i] = $base + (isset($bonusSet[$i]) ? 1 : 0);
        }

        return $result;
    }

    private function validate(array $quantities): void
    {
        if (empty($quantities)) {
            throw InvalidPlanningInputException::empty();
        }

        foreach (array_values($quantities) as $index => $qty) {
            $isNumeric = is_int($qty) || is_float($qty);
            $isWholeNumber = $isNumeric && (float) $qty == floor((float) $qty);
            $isNonNegative = $isNumeric && $qty >= 0;

            if (! $isNumeric || ! $isWholeNumber || ! $isNonNegative) {
                throw InvalidPlanningInputException::negativeOrFraction($index, $qty);
            }
        }
    }
}