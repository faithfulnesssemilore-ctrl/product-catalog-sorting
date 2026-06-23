<?php

declare(strict_types=1);

namespace Tests;

use App\Sorter\DateSorter;
use PHPUnit\Framework\TestCase;

class DateSorterTest extends TestCase
{
    public function testDescendingNewestFirst(): void
    {
        $products = [
            ['created' => '2020-01-01', 'id' => 'old'],
            ['created' => '2025-06-15', 'id' => 'new'],
            ['created' => '2023-03-10', 'id' => 'mid'],
        ];
        $sorter = new DateSorter('desc');
        $sorted = $sorter->sort($products);

        $this->assertSame('new', $sorted[0]['id']);
        $this->assertSame('mid', $sorted[1]['id']);
        $this->assertSame('old', $sorted[2]['id']);
    }

    public function testAscendingOldestFirst(): void
    {
        $products = [
            ['created' => '2025-01-01'],
            ['created' => '2020-01-01'],
        ];
        $sorter = new DateSorter('asc');
        $sorted = $sorter->sort($products);

        $this->assertSame('2020-01-01', $sorted[0]['created']);
        $this->assertSame('2025-01-01', $sorted[1]['created']);
    }
}