<?php

namespace App\Tests\BookAction\Controller;

use App\BookAction\Domain\BookChangeEvent;
use App\Catalog\Model\Book;
use App\Tests\FunctionalTestCase;
use DateTime;
use Ramsey\Uuid\Uuid;

class IndexControllerTest extends FunctionalTestCase
{
    public function testItSuccessfullyRenderPageWithEventsAfter2019()
    {
        $bookTitle = 'Bracia Karamazow';

        $book = new Book($bookTitle);

        $this->entityManager->persist($book);
        $this->entityManager->flush();

        $firstEventBeforeGivenDate = new BookChangeEvent(
            BookChangeEvent::RECEIVE,
            5,
            new DateTime('2019-01-01'),
            $book->getId(),
            $bookTitle
        );

        $this->entityManager->persist($firstEventBeforeGivenDate);
        $this->entityManager->flush();

        $client = static::createClient();
        $client->xmlHttpRequest('GET', '/bookaction/' . $book->getId());
        $response = $client->getResponse();

        self::assertTrue($response->isOk());
    }

    public function testItSuccessfullyRenderPageWithoutEventsAfter2019()
    {
        $bookTitle = 'Bracia Karamazow';

        $book = new Book($bookTitle);

        $this->entityManager->persist($book);
        $this->entityManager->flush();

        $firstEventBeforeGivenDate = new BookChangeEvent(
            BookChangeEvent::RECEIVE,
            5,
            new DateTime('2018-01-01'),
            $book->getId(),
            $bookTitle
        );

        $this->entityManager->persist($firstEventBeforeGivenDate);
        $this->entityManager->flush();

        $client = static::createClient();
        $client->xmlHttpRequest('GET', '/bookaction/' . $book->getId());
        $response = $client->getResponse();

        self::assertTrue($response->isOk());
    }

    public function testItReturnsNotFoundIfBookDoesNotExist()
    {
        $client = static::createClient();
        $client->xmlHttpRequest('GET', '/bookaction/' . Uuid::uuid1()->toString());
        $response = $client->getResponse();

        self::assertSame(404, $response->getStatusCode());
    }
}