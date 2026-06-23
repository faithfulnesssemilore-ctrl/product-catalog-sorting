<?php

declare(strict_types=1);

namespace Tests;

use App\Sorter\ChainedSorter;
use App\Sorter\PriceSorter;
use App\Sorter\DateSorter;
use PHPUnit\Framework\TestCase;

class ChainedSorterTest extends TestCase
{
    public function testTieBreakByDate(): void
    {
        $products = [
            ['price' => 20.00, 'created' => '2025-01-01', 'id' => 1],
            ['price' => 20.00, 'created' => '2024-06-15', 'id' => 2],
            ['price' => 10.00, 'created' => '2023-01-01', 'id' => 3],
        ];
        $chain = new ChainedSorter([
            new PriceSorter('asc'),
            new DateSorter('desc'),
        ]);
        $sorted = $chain->sort($products);

        $this->assertSame(3, $sorted[0]['id']);
        $this->assertSame(1, $sorted[1]['id']);
        $this->assertSame(2, $sorted[2]['id']);
    }

    public function testNoTieDoesNotConsultSecondSorter(): void
    {
        $products = [
            ['price' => 30, 'id' => 1],
            ['price' => 10, 'id' => 2],
        ];
        $chain = new ChainedSorter([
            new PriceSorter('asc'),
            new DateSorter('desc'),
        ]);
        $sorted = $chain->sort($products);

        $this->assertSame(2, $sorted[0]['id']);
        $this->assertSame(1, $sorted[1]['id']);
    }

    public function testRequiresAtLeastTwoSorters(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        new ChainedSorter([new PriceSorter()]);
    }
}