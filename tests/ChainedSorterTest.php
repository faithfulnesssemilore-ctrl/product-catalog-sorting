<?php

declare(strict_types=1);

namespace Tests;

use App\Sorter\ChainedSorter;
use App\Sorter\PriceSorter;
use App\Sorter\SalesPerViewSorter;
use PHPUnit\Framework\TestCase;

class ChainedSorterTest extends TestCase
{
    private array $products;

    protected function setUp(): void
    {
        // Two products with same price (tie) to test chaining
        $this->products = [
            ['id' => 1, 'price' => 20.00, 'created' => '2025-01-01', 'sales_count' => 50, 'views_count' => 100],
            ['id' => 2, 'price' => 20.00, 'created' => '2024-06-15', 'sales_count' => 30, 'views_count' => 100],
            ['id' => 3, 'price' => 10.00, 'created' => '2023-01-01', 'sales_count' => 10, 'views_count' => 100],
        ];
    }

    public function testChainPriceThenSalesPerView(): void
    {
        $priceSorter = new PriceSorter('asc');
        $salesSorter = new SalesPerViewSorter('asc');
        $chain = new ChainedSorter([$priceSorter, $salesSorter], 'asc');

        $sorted = $chain->sort($this->products);

        // First by price asc: id 3 (10.00), then tie between id 1 and id 2 (both 20.00)
        // Tie broken by sales-per-view asc: id 1 ratio = 0.5, id 2 ratio = 0.3
        // So id 2 then id 1
        $this->assertSame(3, $sorted[0]['id']);
        $this->assertSame(2, $sorted[1]['id']);
        $this->assertSame(1, $sorted[2]['id']);
    }

    public function testChainWithDifferentDirections(): void
    {
        $priceSorter = new PriceSorter('asc');
        $salesSorter = new SalesPerViewSorter('desc'); // secondary is descending
        $chain = new ChainedSorter([$priceSorter, $salesSorter]);

        $sorted = $chain->sort($this->products);

        // First price asc: id 3, then id 1 & 2 tie
        // Tie broken by sales-per-view desc: id 1 (0.5) then id 2 (0.3)
        $this->assertSame(3, $sorted[0]['id']);
        $this->assertSame(1, $sorted[1]['id']);
        $this->assertSame(2, $sorted[2]['id']);
    }

    public function testRequiresAtLeastTwoSorters(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        new ChainedSorter([new PriceSorter()]);
    }

    public function testRejectsNonSorterElements(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        new ChainedSorter([new PriceSorter(), 'not a sorter']);
    }
}