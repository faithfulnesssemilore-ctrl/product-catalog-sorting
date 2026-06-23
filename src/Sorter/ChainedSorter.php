<?php
declare(strict_types=1);
namespace App\Sorter;

class ChainedSorter implements SorterInterface
{
    private array $sorters;
    private SortDirection $direction;

    
    public function __construct(array $sorters, string|SortDirection $direction = 'asc')
    {
        if (count($sorters) < 2) {
            throw new \InvalidArgumentException('ChainedSorter requires at least two sorters.');
        }
        foreach ($sorters as $sorter) {
            if (!($sorter instanceof SorterInterface)) {
                throw new \InvalidArgumentException('Each sorter must implement SorterInterface.');
            }
        }
        $this->sorters = array_values($sorters);
        $this->direction = is_string($direction)
            ? SortDirection::fromString($direction)
            : $direction;
    }

    public function sort(array $products): array
    {
        if (count($products) === 0) return [];
        $sorted = $products;
        usort($sorted, function (array $a, array $b): int {
            return $this->compare($a, $b);
        });
        return $sorted;
    }

    public function compare(array $a, array $b): int
    {
        foreach ($this->sorters as $sorter) {
            $result = $sorter->compare($a, $b);
            if ($result !== 0) {
                return $result * $this->direction->multiplier();
            }
        }
        return 0;
    }
}