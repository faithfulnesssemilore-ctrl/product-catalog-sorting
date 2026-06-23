<?php
declare(strict_types=1);
namespace App\Sorter;

class DateSorter extends AbstractSorter
{
    protected function extractValue(array $product): float
    {
        // Convert date string to Unix timestamp (float for consistency)
        return (float) strtotime($product['created'] ?? 'now');
    }
}