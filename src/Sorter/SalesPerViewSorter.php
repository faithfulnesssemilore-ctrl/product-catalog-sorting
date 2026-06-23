<?php
declare(strict_types=1);
namespace App\Sorter;

class SalesPerViewSorter extends AbstractSorter
{
    protected function extractValue(array $product): float
    {
        $sales = (int) ($product['sales_count'] ?? 0);
        $views = (int) ($product['views_count'] ?? 0);
        if ($views === 0) {
            return 0.0;
        }
        return $sales / $views;
    }
}