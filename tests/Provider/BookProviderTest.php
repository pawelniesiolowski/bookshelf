<?php

namespace App\Tests\Provider;

use PHPUnit\Framework\TestCase;
use App\Provider\BookProvider;
use App\Repository\BookRepository;
use App\Entity\Book;

class BookProviderTest extends TestCase
{
    private $bookRepository;

    public function setUp()
    {
        $this->bookRepository = $this->createMock(BookRepository::class);
    }

    public function testItShouldFindOneBook()
    {
        $book = new Book('Zbrodnia i kara', '1234567890', 29.00);
        $this->bookRepository->method('find')
            ->with( $this->equalTo(1))
            ->will($this->returnValue($book));
        $authorProvider = new BookProvider($this->bookRepository);
        $this->assertSame($book, $authorProvider->findOne(1));
    }
}

