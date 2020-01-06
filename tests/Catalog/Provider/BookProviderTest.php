<?php

namespace App\Tests\Catalog\Provider;

use PHPUnit\Framework\TestCase;
use App\Catalog\Provider\BookProvider;
use App\Catalog\Repository\BookRepository;
use App\Catalog\Persistence\Book;
use App\Catalog\Exception\BookException;

class BookProviderTest extends TestCase
{
    private $bookRepository;

    public function setUp(): void
    {
        $this->bookRepository = $this->createMock(BookRepository::class);
    }

    public function testItShouldFindOneBook()
    {
        $book = $this->createMock(Book::class);
        $this->bookRepository->method('find')
            ->with($this->equalTo(1))
            ->will($this->returnValue($book));
        $bookProvider = new BookProvider($this->bookRepository);
        $this->assertSame($book, $bookProvider->findOne(1));
    }

    public function testFindOneShuldReturnBookExceptionWhenBookDoesNotExist()
    {
        $book = $this->createMock(Book::class);
        $this->bookRepository->method('find')
            ->with($this->equalTo(1))
            ->will($this->returnValue(null));
        $bookProvider = new BookProvider($this->bookRepository);
        $this->expectException(BookException::class);
        $bookProvider->findOne(1);
    }
}

