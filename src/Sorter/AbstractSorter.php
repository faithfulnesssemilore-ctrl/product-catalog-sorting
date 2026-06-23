<?php
declare(strict_types=1);

namespace App\Sorter;

abstract class AbstractSorter implements SorterInterface
{
    protected SortDirection $direction;

    
    public function __construct(string|SortDirection $direction = 'asc')// The constructor accepts a sorting direction, which can be either a string ('asc' or 'desc') or a SortDirection enum instance. It converts string input to the corresponding SortDirection enum.
    {
        $this->direction = is_string($direction)
            ? SortDirection::fromString($direction)
            : $direction;  }

    public function sort(array $products): array
    {
        if (count($products) === 0) {
            return [];      }

        $sorted = $products;
        usort($sorted, function (array $a, array $b): int {
            return $this->compare($a, $b);
        });   return $sorted;
    }

    public function compare(array $a, array $b): int
    {
        $valueA = $this->extractValue($a);
        $valueB = $this->extractValue($b);
        return ($valueA <=> $valueB) * $this->direction->multiplier();/}

  
    abstract protected function extractValue(array $product): float;
}