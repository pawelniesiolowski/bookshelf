<?php

namespace App\Tests\Catalog\Controller;

use App\BookAction\Domain\BookChangeEvent;
use App\Tests\FunctionalTestCase;
use App\Catalog\Model\Book;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Ramsey\Uuid\Uuid;

class BookControllerTest extends FunctionalTestCase
{
    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function testItIndexesBooks(): void
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
        $content = json_decode($response->getContent(), true);

        self::assertSame(200, $response->getStatusCode());
        self::assertCount(3, $content['books'] ?? []);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function testItReturnsExistingBook(): void
    {
        $book = new Book('Zbrodnia i kara');
        $this->entityManager->persist($book);
        $this->entityManager->flush();

        $client = static::createClient();
        $client->xmlHttpRequest('GET', '/books/' . $book->getId());
        $response = $client->getResponse();

        self::assertSame(200, $response->getStatusCode());
        self::assertArrayHasKey('book', json_decode($response->getContent(), true));
    }

    public function testItReturnsNotFoundStatusWhenSpecifiedBookDoesNotExist(): void
    {
        $client = static::createClient();
        $client->xmlHttpRequest('GET', '/books/' . Uuid::uuid1()->toString());
        self::assertSame(404, $client->getResponse()->getStatusCode());
    }

    public function testNewShouldCreateNewBookWithGivenCopiesAndReceiveBookChangeEvent(): void
    {
        $bookData = [
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
        $client->xmlHttpRequest('POST', '/books', [], [], [], json_encode($bookData));
        $response = $client->getResponse();

        self::assertSame(201, $response->getStatusCode());

        $bookRepository = $this->entityManager->getRepository(Book::class);
        /** @var Book[] $books */
        $books = $bookRepository->findAll();
        self::assertCount(1, $books);
        self::assertSame(5, intval($books[0]->jsonSerialize()['copies']));

        $bookChangeEventRepository = $this->entityManager->getRepository(BookChangeEvent::class);
        /** @var BookChangeEvent[] $events */
        $events = $bookChangeEventRepository->findAll();
        self::assertStringContainsString('przyjęto', $events[0]->toView()->getName());
        self::assertSame(5, intval($events[0]->toView()->getNum()));
    }

    public function testNewWithInvalidDataShouldReturnResponseWithProperErrors(): void
    {
        $content = [
            'title' => '',
            'ISBN' => 'invalid',
            'price' => '9.99',
            'copies' => '',
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
        $this->assertArrayHasKey('title', $errors['errors']);
        $this->assertArrayHasKey('ISBN', $errors['errors']);
        $this->assertArrayHasKey('authors', $errors['errors']);
    }

    public function testNewBookWithInvalidPriceAndCopiesShouldGetsDefaultValuesWithoutEmittingEvent(): void
    {
        $content = [
            'title' => 'Bracia Karamazow',
            'ISBN' => '',
            'price' => 'invalid',
            'copies' => '-1',
            'authors' => [],
        ];

        $client = static::createClient();
        $client->xmlHttpRequest('POST', '/books', [], [], [], json_encode($content));
        $response = $client->getResponse();

        self::assertSame(201, $response->getStatusCode());

        $bookRepository = $this->entityManager->getRepository(Book::class);
        /** @var Book[] $books */
        $books = $bookRepository->findAll();
        self::assertSame(0, intval($books[0]->jsonSerialize()['price']));
        self::assertSame(0, intval($books[0]->jsonSerialize()['copies']));

        $bookChangeEventRepository = $this->entityManager->getRepository(BookChangeEvent::class);
        self::assertCount(0, $bookChangeEventRepository->findAll());
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function testItShouldEditBook(): void
    {
        $book = new Book('Zbrodnia i zbrodnia');
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
        /** @var Book $book */
        $book = $bookRepository->find($book->getId());
        $this->assertSame('Dostojewski Fiodor "Zbrodnia i kara"', $book->__toString());
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function testItActualizesBookDataForBookEventsAfterEditingBook(): void
    {
        $book = new Book('Zbrodnia i zbrodnia');
        $this->entityManager->persist($book);
        $this->entityManager->flush();

        $client = static::createClient();

        $client->xmlHttpRequest(
            'POST',
            '/receive/' . $book->getId(),
            [],
            [],
            [],
            json_encode(['copies' => '5'])
        );

        $newTitle = 'Zbrodnia i kara';
        $editedBookData = [
            'title' => $newTitle,
            'ISBN' => '',
            'price' => '',
            'authors' => [],
        ];
        $client->xmlHttpRequest('PUT', '/books/' . $book->getId(), [], [], [], json_encode($editedBookData));

        $bookChangeEventsRepository = $this->entityManager->getRepository(BookChangeEvent::class);
        /** @var BookChangeEvent[] $events */
        $events = $bookChangeEventsRepository->findAllByBookId($book->getId());

        foreach ($events as $event) {
            $eventView = $event->toView();
            self::assertSame($newTitle, $eventView->getBookTitle());
        }
    }
}
