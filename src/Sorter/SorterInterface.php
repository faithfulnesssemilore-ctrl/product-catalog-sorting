<?php

declare(strict_types=1);

namespace App\Sorter;

interface SorterInterface
{
    /**
     * Compare two products.
     * Returns negative if $a should come before $b,
     * zero if equal,
     * positive if $a should come after $b.
     *
     * @param array<string, mixed> $a First product
     * @param array<string, mixed> $b Second product
     * @return int
     */
    public function compare(array $a, array $b): int;

    /**
     * Sort an array of products and return a new sorted array.
     *
     * @param array<int, array<string, mixed>> $products
     * @return array<int, array<string, mixed>>
     */
    public function sort(array $products): array;
}