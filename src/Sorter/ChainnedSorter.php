<?php

declare(strict_types=1);

namespace App\Sorter;

use InvalidArgumentException;

class ChainedSorter extends AbstractSorter
{
    /** @var SorterInterface[] */
    private array $sorters;

    public function __construct(array $sorters, string $direction = 'asc')
    {
        if (count($sorters) < 2) {
            throw new InvalidArgumentException(
                'ChainedSorter requires at least two sorters.'
            );
        }

        foreach ($sorters as $sorter) {
            if (!($sorter instanceof SorterInterface)) {
                throw new InvalidArgumentException(
                    'Each element must implement SorterInterface.'
                );
            }
        }

        $this->sorters = array_values($sorters);
        parent::__construct($direction);
    }

    public function compare(array $a, array $b): int
    {
        foreach ($this->sorters as $sorter) {
            $result = $sorter->compareWithDirection($a, $b);
            if ($result !== 0) {
                return $result;
            }
        }
        return 0;
    }
}