<?php

namespace App\Tests\BookAction\Infrastructure;

use App\BookAction\Domain\BookChangeEvent;
use App\Tests\FunctionalTestCase;
use DateTime;
use Ramsey\Uuid\Uuid;

class BookChangeEventRepositoryTest extends FunctionalTestCase
{
    public function testItFindsAllEventsForBook(): void
    {
        $bookId = $this->prepareEvents();
        $bookChangeEventRepository = $this->entityManager->getRepository(BookChangeEvent::class);
        $results = $bookChangeEventRepository->findAllByBookId($bookId);
        self::assertCount(3, $results);
    }

    public function testItFindsAllEventsForBookAfterGivenDate(): void
    {
        $bookId = $this->prepareEvents();
        $bookChangeEventRepository = $this->entityManager->getRepository(BookChangeEvent::class);
        $results = $bookChangeEventRepository->findAllByBookIdAfterDate($bookId, new DateTime('2019-01-01'));
        self::assertCount(2, $results);
    }

    private function prepareEvents(): string
    {
        $bookId = Uuid::uuid1()->toString();
        $bookTitle = 'Bracia Karamazow';

        $firstEventBeforeGivenDate = new BookChangeEvent(
            BookChangeEvent::RECEIVE,
            5,
            new DateTime('2018-12-01'),
            $bookId,
            $bookTitle
        );
        $secondEventAfterGivenDate = new BookChangeEvent(
            BookChangeEvent::SELL,
            3,
            new DateTime('2019-01-01'),
            $bookId,
            $bookTitle
        );
        $firstEventForAnotherBook = new BookChangeEvent(
            BookChangeEvent::RECEIVE,
            5,
            new DateTime('2019-02-01'),
            Uuid::uuid1()->toString(),
            'Zbrodnia i kara'
        );
        $thirdEventAfterGivenDate = new BookChangeEvent(
            BookChangeEvent::RECEIVE,
            7,
            new DateTime('2019-02-01'),
            $bookId,
            $bookTitle
        );

        $this->entityManager->persist($firstEventBeforeGivenDate);
        $this->entityManager->persist($secondEventAfterGivenDate);
        $this->entityManager->persist($firstEventForAnotherBook);
        $this->entityManager->persist($thirdEventAfterGivenDate);
        $this->entityManager->flush();
        return $bookId;
    }
}