<?php
declare(strict_types=1);
namespace App;

use App\Sorter\SorterInterface;

class Catalog
{
    private array $products;

    public function __construct(array $products)
    {
        $this->products = $products;
    }

    public function getProducts(SorterInterface $sorter): array
    {
        return $sorter->sort($this->products);
    }
}