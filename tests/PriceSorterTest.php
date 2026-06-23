<?php

declare(strict_types=1);

namespace Tests;

use App\Sorter\PriceSorter;
use PHPUnit\Framework\TestCase;

class PriceSorterTest extends TestCase
{
    public function testSortAscending(): void
    {
        $products = [
            ['price' => 44.49, 'id' => 1],
            ['price' => 12.99, 'id' => 2],
            ['price' => 10.00, 'id' => 3],
        ];
        $sorter = new PriceSorter('asc');
        $sorted = $sorter->sort($products);
        $this->assertSame(10.00, $sorted[0]['price']);
        $this->assertSame(12.99, $sorted[1]['price']);
        $this->assertSame(44.49, $sorted[2]['price']);
    }

    public function testSortDescending(): void
    {
        $products = [
            ['price' => 10.00],
            ['price' => 44.49],
            ['price' => 12.99],
        ];
        $sorter = new PriceSorter('desc');
        $sorted = $sorter->sort($products);
        $this->assertSame(44.49, $sorted[0]['price']);
        $this->assertSame(12.99, $sorted[1]['price']);
        $this->assertSame(10.00, $sorted[2]['price']);
    }

    public function testInvalidDirectionThrowsException(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        new PriceSorter('backwards');
    }

    public function testOriginalArrayUntouched(): void
    {
        $original = [['price' => 5], ['price' => 3]];
        $sorter = new PriceSorter('asc');
        $sorter->sort($original);
        $this->assertSame([['price' => 5], ['price' => 3]], $original);
    }
}