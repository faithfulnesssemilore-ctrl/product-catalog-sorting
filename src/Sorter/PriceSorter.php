<?php
declare(strict_types=1);
namespace App\Sorter;

class PriceSorter extends AbstractSorter
{
    protected function extractValue(array $product): float
    {
        return (float) ($product['price'] ?? 0.0);
    }
}