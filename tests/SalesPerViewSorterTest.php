<?php

declare(strict_types=1);

namespace Tests;

use App\Sorter\SalesPerViewSorter;
use PHPUnit\Framework\TestCase;

class SalesPerViewSorterTest extends TestCase
{
    public function testAscendingOrder(): void
    {
        $products = [
            ['sales_count' => 32, 'views_count' => 730],
            ['sales_count' => 301, 'views_count' => 3279],
            ['sales_count' => 1048, 'views_count' => 20123],
        ];
        $sorter = new SalesPerViewSorter('asc');
        $sorted = $sorter->sort($products);

        $this->assertSame(32, $sorted[0]['sales_count']);
        $this->assertSame(1048, $sorted[1]['sales_count']);
        $this->assertSame(301, $sorted[2]['sales_count']);
    }

    public function testZeroViewsProductRatioIsZero(): void
    {
        $products = [
            ['sales_count' => 100, 'views_count' => 0, 'id' => 'A'],
            ['sales_count' => 1, 'views_count' => 1, 'id' => 'B'],
        ];
        $sorter = new SalesPerViewSorter('asc');
        $sorted = $sorter->sort($products);

        $this->assertSame('A', $sorted[0]['id']);
        $this->assertSame('B', $sorted[1]['id']);
    }
}