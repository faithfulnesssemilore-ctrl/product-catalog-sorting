<?php

declare(strict_types=1);

namespace Tests;

use App\Sorter\PriceSorter;
use PHPUnit\Framework\TestCase;

class PriceSorterTest extends TestCase
{
    /** @var array */
    private array $products;

    protected function setUp(): void
    {
        $this->products = [
            ['id' => 1, 'price' => 44.49],
            ['id' => 2, 'price' => 12.99],
            ['id' => 3, 'price' => 10.00],
        ];
    }

    public function testSortAscending(): void
    {
        $sorter = new PriceSorter('asc');
        $sorted = $sorter->sort($this->products);

        $this->assertSame(10.00, $sorted[0]['price']);
        $this->assertSame(12.99, $sorted[1]['price']);
        $this->assertSame(44.49, $sorted[2]['price']);
    }

    public function testSortDescending(): void
    {
        $sorter = new PriceSorter('desc');
        $sorted = $sorter->sort($this->products);

        $this->assertSame(44.49, $sorted[0]['price']);
        $this->assertSame(12.99, $sorted[1]['price']);
        $this->assertSame(10.00, $sorted[2]['price']);
    }

    public function testInvalidDirectionThrowsException(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        new PriceSorter('invalid');
    }
}
