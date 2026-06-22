<?php

declare(strict_types=1);

namespace App\Sorter;

class SalesPerViewSorter extends AbstractSorter
{
    
     // Compare two products by their sales-per-view ratio.
     //Products with zero views get ratio 0.0.
     
    public function compare(array $a, array $b): int
    {
        $ratioA = $this->calculateRatio($a);
        $ratioB = $this->calculateRatio($b);

        return $ratioA <=> $ratioB;
    }

    
     *// Calculate sales-per-view ratio for a product.
     *// If views_count is zero, ratio is 0.0 (avoid division by zero).
     */
    private function calculateRatio(array $product): float
    {
        $salesCount = (int) ($product['sales_count'] ?? 0);
        $viewsCount = (int) ($product['views_count'] ?? 0);

        if ($viewsCount === 0) {
            return 0.0;
        }

        return $salesCount / $viewsCount;
    }
}
