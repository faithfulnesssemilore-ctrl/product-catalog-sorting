<?php

declare(strict_types=1);

namespace Tests;

use App\Sorter\SalesPerViewSorter;
use PHPUnit\Framework\TestCase;

class SalesPerViewSorterTest extends TestCase
{
    private array $products;

    protected function setUp(): void
    {
        $this->products = [
            ['id' => 1, 'sales_count' => 32,   'views_count' => 730],
            ['id' => 2, 'sales_count' => 301,  'views_count' => 3279],
            ['id' => 3, 'sales_count' => 1048, 'views_count' => 20123],
            ['id' => 4, 'sales_count' => 10,   'views_count' => 0],      // zero views
            ['id' => 5, 'sales_count' => 0,    'views_count' => 100],    // zero sales
        ];
    }

    public function testSortAscending(): void
    {
        $sorter = new SalesPerViewSorter('asc');
        $sorted = $sorter->sort($this->products);

        // Expected order: id 5 (ratio 0), id 4 (ratio 0), id 1 (0.0438...), id 2 (0.0918...), id 3 (0.0520...)
        // Let's compute exact ratios:
        // id 5: 0/100 = 0.0
        // id 4: 0.0 (special zero views rule)
        // id 1: 32/730 ≈ 0.0438
        // id 2: 301/3279 ≈ 0.0918
        // id 3: 1048/20123 ≈ 0.0520
        // Ascending order: 0.0, 0.0, 0.0438, 0.0520, 0.0918 => ids: 5,4,1,3,2
        $this->assertSame(5, $sorted[0]['id']);
        $this->assertSame(4, $sorted[1]['id']);
        $this->assertSame(1, $sorted[2]['id']);
        $this->assertSame(3, $sorted[3]['id']);
        $this->assertSame(2, $sorted[4]['id']);
    }

    public function testSortDescending(): void
    {
        $sorter = new SalesPerViewSorter('desc');
        $sorted = $sorter->sort($this->products);

        // Descending: highest ratio first
        $this->assertSame(2, $sorted[0]['id']);
        $this->assertSame(3, $sorted[1]['id']);
        $this->assertSame(1, $sorted[2]['id']);
        // Zero ratios last (or first depending on direction? Since 0.0 is smallest, ascending puts them first, descending puts them last)
        // Actually: descending flips so 0.0 is largest? No, 0.0 is the smallest number. Descending means largest first, so 0.0 goes to the end.
        $this->assertSame(5, $sorted[3]['id']);
        $this->assertSame(4, $sorted[4]['id']);
    }

    public function testZeroViewsHandling(): void
    {
        $sorter = new SalesPerViewSorter('asc');
        $sorted = $sorter->sort([
            ['id' => 'a', 'sales_count' => 100, 'views_count' => 0],
            ['id' => 'b', 'sales_count' => 10,  'views_count' => 5],
        ]);
        // a has ratio 0.0, b has ratio 2.0. Ascending: a before b
        $this->assertSame('a', $sorted[0]['id']);
        $this->assertSame('b', $sorted[1]['id']);
    }

    public function testInvalidDirectionThrowsException(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        new SalesPerViewSorter('upwards');
    }
}