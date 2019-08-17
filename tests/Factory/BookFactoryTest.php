<?php

namespace App\Factory;

use PHPUnit\Framework\TestCase;
use App\Factory\BookFactory;
use App\Entity\Book;
use App\Provider\AuthorProvider;

class BookFactoryTest extends TestCase
{
    private $bookFactory;

    public function setUp()
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
            'copies' => 5,
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
        $jsonSerializedBook = $book->jsonSerializeExtended();
        $this->assertArraySubset($bookData, $jsonSerializedBook);
        $this->assertContains('przyjęto', $jsonSerializedBook['events'][0]);
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
            'copies' => 0,
        ];
        $data = json_encode($requestContent);
        $book = $this->bookFactory->fromJson($data);
        $this->assertInstanceOf(Book::class, $book);
    }
}

