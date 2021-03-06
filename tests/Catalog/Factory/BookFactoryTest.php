<?php

namespace App\Tests\Catalog\Factory;

use App\Catalog\Factory\BookFactory;
use PHPUnit\Framework\TestCase;
use App\Catalog\Model\Book;

class BookFactoryTest extends TestCase
{
    private $bookFactory;

    public function setUp(): void
    {
        $this->bookFactory = new BookFactory();
    }

    public function testItCreatesBookFromJson()
    {
        $requestContent = [
            'title' => 'Zbrodnia i kara',
            'ISBN' => '1234567890',
            'price' => 39.99,
            'authors' => [
                [
                    'name' => 'Fiodor',
                    'surname' => 'Dostojewski',
                ],
            ],
        ];
        $requestContent = json_encode($requestContent);

        $book = $this->bookFactory->fromJson($requestContent);
        $this->assertInstanceOf(Book::class, $book);

        $bookData = [
            'title' => 'Zbrodnia i kara',
            'ISBN' => '1234567890',
            'price' => 39.99,
            'authors' => [
                [
                    'name' => 'Fiodor',
                    'surname' => 'Dostojewski',
                ],
            ],
            'id' => null,
            'copies' => 0,
        ];
        $this->assertEquals($bookData, $book->jsonSerialize());
    }

    public function testItCreatesBookFromEmptyData()
    {
        $book = $this->bookFactory->fromJson('');
        $this->assertInstanceOf(Book::class, $book);
    }

    public function testItCreatesBookWhenPriceAndISBNAreEmptyStrings()
    {
        $requestContent = [
            'title' => 'Zbrodnia i kara',
            'ISBN' => '',
            'price' => '',
        ];
        $data = json_encode($requestContent);
        $book = $this->bookFactory->fromJson($data);
        $this->assertInstanceOf(Book::class, $book);
    }
}

