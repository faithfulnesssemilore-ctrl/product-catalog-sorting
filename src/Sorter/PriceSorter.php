<?php

declare(strict_types=1);

namespace App\Sorter;

class PriceSorter extends AbstractSorter
{
    /**
     * Compare two products by their 'price' field.
     *
     * @param array<string, mixed> $a
     * @param array<string, mixed> $b
     * @return int
     */
    public function compare(array $a, array $b): int
    {
        $priceA = $a['price'] ?? 0.0;
        $priceB = $b['price'] ?? 0.0;

        return $priceA <=> $priceB;
    }
}