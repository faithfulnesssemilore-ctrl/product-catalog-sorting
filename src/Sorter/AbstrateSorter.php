<?php

declare(strict_types=1);

namespace App\Sorter;

use InvalidArgumentException;

abstract class AbstractSorter implements SorterInterface
{
    protected string $direction;

    private const ALLOWED_DIRECTIONS = ['asc', 'desc'];

    /**
     * @param string $direction 'asc' or 'desc' (case-insensitive)
     * @throws InvalidArgumentException if direction is invalid
     */
    public function __construct(string $direction = 'asc')
    {
        $direction = strtolower($direction);

        if (!in_array($direction, self::ALLOWED_DIRECTIONS, true)) {
            throw new InvalidArgumentException(
                sprintf(
                    "Invalid sort direction: '%s'. Allowed: 'asc' or 'desc'.",
                    $direction
                )
            );
        }

        $this->direction = $direction;
    }
/**
 * Compare with direction applied. This is used by sort() and by ChainedSorter.
 * It calls the abstract compare() and flips for direction.
 */
protected function compareWithDirection(array $a, array $b): int
{
    $result = $this->compare($a, $b);
    return $this->direction === 'desc' ? -$result : $result;
}
    /**
     * Sort products using this sorter's compare() method.
     * Returns a NEW sorted array, leaving the original unchanged.
     */
public function sort(array $products): array
{
    if (count($products) === 0) {
        return [];
    }
    $sorted = $products;
    usort($sorted, function (array $a, array $b): int {
        return $this->compareWithDirection($a, $b);
    });
    return $sorted;
}

    /**
     * Concrete sorters implement this to define their comparison logic.
     * Should return negative if $a before $b, zero if equal, positive if $a after $b.
     * This should always compare in "natural ascending" order;
     * the sort() method handles direction flipping.
     */
public function compare(array $a, array $b): int
{
    foreach ($this->sorters as $sorter) {
        // Use compareWithDirection to respect each sorter's direction
        $result = $sorter->compareWithDirection($a, $b);
        if ($result !== 0) {
            return $result;
        }
    }
    return 0;
}
}