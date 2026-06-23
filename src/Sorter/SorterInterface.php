<?php
declare(strict_types=1);// A strict type checking instruction.

namespace App\Sorter;

interface SorterInterface
{
    public function compare(array $a, array $b): int;

    public function sort(array $products): array;
}