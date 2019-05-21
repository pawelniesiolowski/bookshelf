<?php

namespace App\Factory;

use PHPUnit\Framework\TestCase;
use App\Factory\BookFactory;
use App\Entity\Book;
use App\Provider\AuthorProvider;

class BookFactoryTest extends TestCase
{
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

        $authorProvider = $this->createMock(AuthorProvider::class);
        $authorProvider->method('findOneByNameAndSurname')
            ->will($this->returnValue(null));

        $bookFactory = new BookFactory($authorProvider);

        $book = $bookFactory->fromJson($requestContent);
        $this->assertInstanceOf(Book::class, $book);

        $bookData = [
            'title' => 'Zbrodnia i kara',
            'ISBN' => '1234567890',
            'price' => 39.99,
            'authors' => [
                'Dostojewski Fiodor',
            ],
        ];
        $jsonSerializedBook = $book->jsonSerialize();
        $this->assertArraySubset($bookData, $jsonSerializedBook);
        $this->assertContains('przyjęto', $jsonSerializedBook['events'][0]);
    }

    public function testItCreatesBookFromEmptyData()
    {
        $authorProvider = $this->createMock(AuthorProvider::class);
        $authorProvider->method('findOneByNameAndSurname')
            ->will($this->returnValue(null));
        
        $bookFactory = new BookFactory($authorProvider);

        $book = $bookFactory->fromJson('');
        $this->assertInstanceOf(Book::class, $book);
    }
}

