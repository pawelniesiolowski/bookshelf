<?php

namespace App\Tests\Controller;

use App\Tests\FunctionalTestCase;
use App\Entity\Author;
use App\Entity\Book;
use App\Repository\BookRepository;
use App\Repository\BookChangeEventRepository;

class BookControllerTest extends FunctionalTestCase
{
    public function testNew()
    {
        $content = [
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
        $client = static::createClient();
        $client->xmlHttpRequest('POST', '/books', [], [], [], json_encode($content));
        $response = $client->getResponse();

        $this->assertSame(201, $response->getStatusCode());

        $bookRepository = new BookRepository($this->registry);
        $books = $bookRepository->findAll();
        $this->assertSame('Dostojewski Fiodor "Zbrodnia i kara"', $books[0]->__toString());

        $bookChangeEventRepository = new BookChangeEventRepository($this->registry);
        $events = $bookChangeEventRepository->findAll();
        $this->assertContains('przyjęto', $events[0]->__toString());
    }

    public function testNewWithInvalidDataShouldCauseResponseWithProperErrors()
    {
        $content = [
            'title' => '',
            'ISBN' => 'invalid',
            'price' => 0.9999,
            'copies' => -1,
            'authors' => [
                [
                    'name' => '',
                    'surname' => '',
                ],
            ],
        ];

        $client = static::createClient();
        $client->xmlHttpRequest('POST', '/books', [], [], [], json_encode($content));
        $response = $client->getResponse();

        $this->assertSame(422, $response->getStatusCode());
        $errors = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('errors', $errors);
    }

    public function testItShouldEditBook()
    {
        $book = new Book('Zbrodnia i kara', '0123456789', 19.99);
        $this->entityManager->persist($book);
        $this->entityManager->flush();

        $content = [
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
        $client = static::createClient();
        $client->xmlHttpRequest('PUT', '/books/1', [], [], [], json_encode($content));
        $response = $client->getResponse();

        $this->assertSame(204, $response->getStatusCode());

        $bookRepository = new BookRepository($this->registry);
        $book = $bookRepository->find(1);
        $this->assertSame('Dostojewski Fiodor "Zbrodnia i kara"', $book->__toString());
    }
}

