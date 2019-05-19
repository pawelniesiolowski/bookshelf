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
}

