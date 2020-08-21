<?php

namespace App\Tests\BookAction\Domain\UseCase;

use App\BookAction\Domain\BookChangeEvent;
use App\BookAction\Domain\UseCase\ActualizeBookDataService;
use App\BookAction\Infrastructure\InMemoryBookChangeEventProvider;
use App\BookAction\Infrastructure\InMemoryBookChangeEventWriter;
use App\Catalog\UseCase\BookDTO;
use DateTime;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

class ActualizeBookDataServiceTest extends TestCase
{
    public function testItActualizeBookDataForAllBookEvents(): void
    {
        $bookId = Uuid::uuid1()->toString();
        $bookTitle = 'Karamazow';
        $bookAuthor = 'Dostojewski';

        $newTitle = 'Bracia Karamazow';
        $newAuthor = 'Fiodor Dostojewski';

        $firstEvent = new BookChangeEvent(
            BookChangeEvent::RECEIVE,
            5,
            new DateTime('2019-01-01'),
            $bookId,
            $bookTitle,
            $bookAuthor
        );
        $secondEvent = new BookChangeEvent(
            BookChangeEvent::RECEIVE,
            5,
            new DateTime('2019-02-01'),
            $bookId,
            $bookTitle,
            $bookAuthor
        );

        $inMemoryBookChangeEventProvider = new InMemoryBookChangeEventProvider();
        $inMemoryBookChangeEventProvider->addEvent($firstEvent, $bookId);
        $inMemoryBookChangeEventProvider->addEvent($secondEvent, $bookId);

        $inMemoryBookChangeEventWriter = new InMemoryBookChangeEventWriter();

        $actualizeBookDataService = new ActualizeBookDataService(
            $inMemoryBookChangeEventProvider,
            $inMemoryBookChangeEventWriter
        );

        $bookDTO = new BookDTO($bookId, $newTitle, $newAuthor);
        $actualizeBookDataService->actualizeDataForBook($bookDTO);
        $bookChangeEventsViews = $inMemoryBookChangeEventWriter->getAllViews();

        $this->assertSame($newTitle, $bookChangeEventsViews[0]->getBookTitle());
        $this->assertSame($newTitle, $bookChangeEventsViews[0]->getBookTitle());
        $this->assertSame($newAuthor, $bookChangeEventsViews[1]->getBookAuthor());
        $this->assertSame($newAuthor, $bookChangeEventsViews[1]->getBookAuthor());
    }
}