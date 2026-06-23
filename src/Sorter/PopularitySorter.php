<?php
declare(strict_types=1);
namespace App\Sorter;

class PopularitySorter extends AbstractSorter
{
    protected function extractValue(array $product): float
    {
        return (float) ($product['sales_count'] ?? 0);
    }
}