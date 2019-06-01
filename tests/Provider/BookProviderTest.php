<?php

namespace App\Tests\Provider;

use PHPUnit\Framework\TestCase;
use App\Provider\BookProvider;
use App\Repository\BookRepository;
use App\Entity\Book;
use App\Exception\BookException;

class BookProviderTest extends TestCase
{
    private $bookRepository;

    public function setUp()
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

    public function testItCansGetAllOrderedByAuthorAndTitle()
    {
        $firstBook = $this->createMock(Book::class);
        $firstBook->method('__toString')
            ->will($this->returnValue('C'));
        $secondBook = $this->createMock(Book::class);
        $secondBook->method('__toString')
            ->will($this->returnValue('A'));
        $thirdBook = $this->createMock(Book::class);
        $thirdBook->method('__toString')
            ->will($this->returnValue('B'));
        $booksOrderedByTitle = [$firstBook, $secondBook, $thirdBook];
        $this->bookRepository->method('findAllOrderedByTitle')
            ->will($this->returnValue($booksOrderedByTitle));
        $bookProvider = new BookProvider($this->bookRepository);
        $booksOrderedByAuthor = [$secondBook, $thirdBook, $firstBook];
        $this->assertSame($booksOrderedByAuthor, $bookProvider->getAllOrderedByAuthorAndTitle());
    }
}

