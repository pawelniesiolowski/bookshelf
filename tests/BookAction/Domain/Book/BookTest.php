<?php

namespace App\Tests\BookAction\Domain\Book;

use App\BookAction\Domain\Book\Book;
use App\BookAction\Domain\Book\Copies;
use App\Catalog\UseCase\BookDTO;
use App\Receiver\UseCase\ReceiverDTO;
use DateTime;
use DomainException;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;
use App\BookAction\Domain\BookChangeEvent;

class BookTest extends TestCase
{
    public function testItThrowsExceptionWhenReceiveZeroCopies(): void
    {
        $book = new Book([], new BookDTO(Uuid::uuid1()->toString(), 'Test', ''));
        $this->expectException(DomainException::class);
        $book->receive(new Copies(0));
    }

    public function testItComputesNewNumberOfCopiesAfterReceivingBooks(): void
    {
        $bookId = Uuid::uuid1()->toString();
        $bookTitle = 'Bracia Karamazow';

        $bookDTO = new BookDTO($bookId, $bookTitle, '');
        $bookEvents = $this->prepareReceiveEvents($bookId, $bookTitle);

        $book = new Book($bookEvents, $bookDTO);
        $book->receive(new Copies(5));

        self::assertSame(15, $book->copies()->toInt());
    }

    public function testItCreatesReceiveEventWithProperNumberOfCopiesForSingleEvent(): void
    {
        $bookId = Uuid::uuid1()->toString();
        $bookTitle = 'Bracia Karamazow';

        $bookDTO = new BookDTO($bookId, $bookTitle, '');
        $bookEvents = $this->prepareReceiveEvents($bookId, $bookTitle);

        $book = new Book($bookEvents, $bookDTO);
        $bookChangeEvent = $book->receive(new Copies(5));

        self::assertSame(5, $bookChangeEvent->copies()->toInt());
    }

    public function testItThrowsExceptionWhenReleaseZeroCopies(): void
    {
        $book = new Book([], new BookDTO(Uuid::uuid1()->toString(), 'Test', ''));
        $this->expectException(DomainException::class);
        $book->release(new Copies(0), $this->prepareReceiver(), '');
    }

    public function testItComputesNewNumberOfCopiesAfterReleasingBooks(): void
    {
        $bookId = Uuid::uuid1()->toString();
        $bookTitle = 'Bracia Karamazow';

        $bookDTO = new BookDTO($bookId, $bookTitle, '');
        $bookEvents = $this->prepareReceiveEvents($bookId, $bookTitle);

        $book = new Book($bookEvents, $bookDTO);
        $book->release(new Copies(3), $this->prepareReceiver(), '');

        self::assertSame(7, $book->copies()->toInt());
    }

    public function testItCreatesReleaseEventWithProperNumberOfCopiesForSingleEvent(): void
    {
        $bookId = Uuid::uuid1()->toString();
        $bookTitle = 'Bracia Karamazow';

        $bookDTO = new BookDTO($bookId, $bookTitle, '');
        $bookEvents = $this->prepareReceiveEvents($bookId, $bookTitle);

        $book = new Book($bookEvents, $bookDTO);
        $bookChangeEvent = $book->release(new Copies(5), $this->prepareReceiver(), '');

        self::assertSame(5, $bookChangeEvent->copies()->toInt());
    }

    public function testItComputesNewNumberOfCopiesAfterSellingBooks(): void
    {
        $bookId = Uuid::uuid1()->toString();
        $bookTitle = 'Bracia Karamazow';

        $bookDTO = new BookDTO($bookId, $bookTitle, '');
        $bookEvents = $this->prepareReceiveEvents($bookId, $bookTitle);

        $book = new Book($bookEvents, $bookDTO);
        $book->sell(new Copies(7), '');

        self::assertSame(3, $book->copies()->toInt());
    }

    public function testItCreatesSellEventWithProperNumberOfCopiesForSingleEvent(): void
    {
        $bookId = Uuid::uuid1()->toString();
        $bookTitle = 'Bracia Karamazow';

        $bookDTO = new BookDTO($bookId, $bookTitle, '');
        $bookEvents = $this->prepareReceiveEvents($bookId, $bookTitle);

        $book = new Book($bookEvents, $bookDTO);
        $bookChangeEvent = $book->sell(new Copies(2), '');

        self::assertSame(2, $bookChangeEvent->copies()->toInt());
    }

    public function testItComputesNumberOfCopiesWithDifferentEventsAtTheBeginning(): void
    {
        $bookId = Uuid::uuid1()->toString();
        $bookTitle = 'Bracia Karamazow';

        $receiveEvent = new BookChangeEvent(
            BookChangeEvent::RECEIVE,
            5,
            new DateTime('2019-01-01'),
            $bookId,
            $bookTitle
        );
        $releaseEvent = new BookChangeEvent(
            BookChangeEvent::RELEASE,
            2,
            new DateTime('2019-01-02'),
            $bookId,
            $bookTitle,
            null,
            Uuid::uuid1()->toString(),
            'Niesiołowski Paweł'
        );
        $sellEvent = new BookChangeEvent(
            BookChangeEvent::SELL,
            1,
            new DateTime('2019-01-02'),
            $bookId,
            $bookTitle
        );
        $sellEvent->setComment('Sprzedaż w księgarni internetowej');
        $events = [
            $receiveEvent,
            $releaseEvent,
            $sellEvent,
        ];

        $bookDTO = new BookDTO($bookId, $bookTitle, '');

        $book = new Book($events, $bookDTO);

        self::assertSame(2, $book->copies()->toInt());
    }

    private function prepareReceiver(): ReceiverDTO
    {
        return new ReceiverDTO(Uuid::uuid1()->toString(), 'Niesiołowski Paweł');
    }

    private function prepareReceiveEvents(string $bookId, string $bookTitle): array
    {
        $firstEvent = new BookChangeEvent(
            BookChangeEvent::RECEIVE,
            5,
            new DateTime('2019-01-01'),
            $bookId,
            $bookTitle
        );
        $secondEvent = new BookChangeEvent(
            BookChangeEvent::RECEIVE,
            5,
            new DateTime('2019-01-01'),
            $bookId,
            $bookTitle
        );
        return [$firstEvent, $secondEvent];
    }
}
