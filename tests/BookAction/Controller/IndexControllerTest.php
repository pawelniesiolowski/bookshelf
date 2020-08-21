<?php

namespace App\Tests\BookAction\Controller;

use App\BookAction\Domain\BookChangeEvent;
use App\Tests\FunctionalTestCase;
use DateTime;
use Ramsey\Uuid\Uuid;

class IndexControllerTest extends FunctionalTestCase
{
    public function testItIndexesActionsForBookFromTheBeginningOf2019()
    {
        $bookId = Uuid::uuid1()->toString();
        $bookTitle = 'Bracia Karamazow';

        $firstEventBeforeGivenDate = new BookChangeEvent(
            BookChangeEvent::RECEIVE,
            5,
            new DateTime('2019-01-01'),
            $bookId,
            $bookTitle
        );

        $this->entityManager->persist($firstEventBeforeGivenDate);
        $this->entityManager->flush();

        $client = static::createClient();
        $client->xmlHttpRequest('GET', '/bookaction/' . $bookId);
        $response = $client->getResponse();
        self::assertTrue($response->isOk());
    }
}