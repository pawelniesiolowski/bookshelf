<?php

namespace App\Catalog\Factory;

use PHPUnit\Framework\TestCase;
use App\Catalog\Persistence\Book;
use App\Catalog\Provider\AuthorProvider;

class BookFactoryTest extends TestCase
{
    private $bookFactory;

    public function setUp(): void
    {
        $authorProvider = $this->createMock(AuthorProvider::class);
        $authorProvider->method('findOneByNameAndSurname')
            ->will($this->returnValue(null));
        $this->bookFactory = new BookFactory($authorProvider);
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
        ];
        $this->assertArraySubset($bookData, $book->jsonSerialize());
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

