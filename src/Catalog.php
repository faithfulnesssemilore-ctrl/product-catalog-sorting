<?php

declare(strict_types=1);

namespace App;

use App\Sorter\SorterInterface;

class Catalog
{
    /** @var array<int, array<string, mixed>> */
    private array $products;

    /**
     * @param array<int, array<string, mixed>> $products
     */
    public function __construct(array $products)
    {
        $this->products = $products;
    }

    /**
     * Return products sorted by the given sorter.
     */
    public function getProducts(SorterInterface $sorter): array
    {
        return $sorter->sort($this->products);
    }
}