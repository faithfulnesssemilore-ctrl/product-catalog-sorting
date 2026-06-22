<?php

declare(strict_types=1);

namespace Tests;

use App\Sorter\PriceSorter;
use App\Sorter\SorterRegistry;
use PHPUnit\Framework\TestCase;

class SorterRegistryTest extends TestCase
{
    public function testRegisterAndRetrieve(): void
    {
        $registry = new SorterRegistry();
        $sorter = new PriceSorter('asc');
        $registry->register('price_asc', $sorter);

        $this->assertSame($sorter, $registry->get('price_asc'));
    }

    public function testHasKey(): void
    {
        $registry = new SorterRegistry();
        $registry->register('price', new PriceSorter());

        $this->assertTrue($registry->has('price'));
        $this->assertFalse($registry->has('missing'));
    }

    public function testMissingKeyThrowsException(): void
    {
        $registry = new SorterRegistry();
        $this->expectException(\InvalidArgumentException::class);
        $registry->get('nonexistent');
    }

    public function testEmptyKeyThrowsException(): void
    {
        $registry = new SorterRegistry();
        $this->expectException(\InvalidArgumentException::class);
        $registry->register('', new PriceSorter());
    }
}