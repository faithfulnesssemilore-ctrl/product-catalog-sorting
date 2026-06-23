<?php

declare(strict_types=1);

namespace Tests;

use App\Sorter\SorterRegistry;
use App\Sorter\PriceSorter;
use PHPUnit\Framework\TestCase;

class SorterRegistryTest extends TestCase
{
    public function testRegisterAndGet(): void
    {
        $reg = new SorterRegistry();
        $reg->register('price', new PriceSorter());
        $this->assertInstanceOf(PriceSorter::class, $reg->get('price'));
    }

    public function testUnknownKeyThrowsException(): void
    {
        $reg = new SorterRegistry();
        $this->expectException(\InvalidArgumentException::class);
        $reg->get('missing');
    }

    public function testHasKey(): void
    {
        $reg = new SorterRegistry();
        $reg->register('test', new PriceSorter());
        $this->assertTrue($reg->has('test'));
        $this->assertFalse($reg->has('nope'));
    }
}