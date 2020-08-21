<?php

namespace App\Tests\BookAction\Domain\Book;

use App\BookAction\Domain\Book\Copies;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class CopiesTest extends TestCase
{
    public function testItThrowsExceptionIfIsConstructedWithCopiesEqualsLessThanZero(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new Copies(-1);
    }

    public function testItAddsCopiesToCopies(): void
    {
        $copies = new Copies(0);
        $copies = $copies->add(new Copies(3));
        $copies = $copies->add(new Copies(3));
        $this->assertSame(6, $copies->toInt());
    }

    public function testItSubtractsCopies(): void
    {
        $copies = new Copies(10);
        $copies = $copies->subtract(new Copies(3));
        $copies = $copies->subtract(new Copies(3));
        $this->assertSame(4, $copies->toInt());
    }

    public function testItThrowsExceptionWhenSubtractsCopiesBelowZero(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $copies = new Copies(10);
        $copies = $copies->subtract(new Copies(3));
        $copies->subtract(new Copies(8));
    }
}