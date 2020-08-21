<?php

namespace App\Tests\Catalog\Controller;

use App\BookAction\Domain\BookChangeEvent;
use App\Tests\FunctionalTestCase;
use App\Catalog\Model\Book;
use Ramsey\Uuid\Uuid;

class BookControllerTest extends FunctionalTestCase
{
    public function testItIndexesBooks()
    {
        $crime = new Book('Zbrodnia i kara');
        $idiot = new Book('Idiota');
        $robots = new Book('Bajki robotów');
        $this->entityManager->persist($crime);
        $this->entityManager->persist($robots);
        $this->entityManager->persist($idiot);
        $this->entityManager->flush();

        $client = static::createClient();
        $client->xmlHttpRequest('GET', '/books');
        $response = $client->getResponse();

        $this->assertTrue($response->isOk());
    }

    public function testItReturnsNotFoundWhenSpecifiedBookDoesNotExist()
    {
        $id = Uuid::uuid1()->toString();
        $client = static::createClient();
        $client->xmlHttpRequest('GET', '/books/' . $id);
        $response = $client->getResponse();
        self::assertSame(404, $response->getStatusCode());
    }

    public function testNewShouldCreateNewBookAndMaybeBookChangeEvent()
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

        $bookRepository = $this->entityManager->getRepository(Book::class);
        $books = $bookRepository->findAll();
        $this->assertSame('Dostojewski Fiodor "Zbrodnia i kara"', $books[0]->__toString());

        $bookChangeEventRepository = $this->entityManager->getRepository(BookChangeEvent::class);
        $events = $bookChangeEventRepository->findAll();
        $this->assertStringContainsString('przyjęto', $events[0]->__toString());
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
        $book = new Book('Zbrodnia i kara');
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
        $client->xmlHttpRequest('PUT', '/books/' . $book->getId(), [], [], [], json_encode($content));
        $response = $client->getResponse();

        $this->assertSame(204, $response->getStatusCode());

        $bookRepository = $this->entityManager->getRepository(Book::class);
        $book = $bookRepository->find($book->getId());
        $this->assertSame('Dostojewski Fiodor "Zbrodnia i kara"', $book->__toString());
    }
}
