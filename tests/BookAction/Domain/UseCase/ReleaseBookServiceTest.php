<?php

namespace App\Tests\BookAction\Domain\UseCase;

use App\Catalog\UseCase\BookDTO;
use App\Receiver\UseCase\ReceiverDTO;
use DateTime;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use App\BookAction\Domain\UseCase\ReleaseBookService;
use App\BookAction\Infrastructure\InMemoryBookChangeEventProvider;
use App\BookAction\Infrastructure\InMemoryBookChangeEventWriter;
use App\BookAction\Domain\BookChangeEvent;
use Ramsey\Uuid\Uuid;
use App\BookAction\Domain\Book\Copies;

class ReleaseBookServiceTest extends TestCase
{
    public function testItSubtractCopiesForBook(): void
    {
        $bookId = Uuid::uuid1()->toString();
        $bookTitle = 'Bracia Karamazow';
        $receiveEvent = new BookChangeEvent(
            BookChangeEvent::RECEIVE,
            10,
            new DateTime('2019-01-01'),
            $bookId,
            $bookTitle
        );
        $bookChangeEventProvider = new InMemoryBookChangeEventProvider();
        $bookChangeEventProvider->addEvent($receiveEvent, $bookId);
        $bookChangeEventWriter = new InMemoryBookChangeEventWriter();

        $releaseBookService = new ReleaseBookService(
            $bookChangeEventProvider,
            $bookChangeEventWriter
        );

        $copies = new Copies(7);
        $bookDTO = new BookDTO($bookId, $bookTitle, '');

        $newNumberOfBookCopies = $releaseBookService->subtractCopiesForBook(
            $copies,
            $bookDTO,
            $this->prepareReceiver(),
            ''
        );
        self::assertSame(3, $newNumberOfBookCopies->toInt());
    }

    public function testItThrowsExceptionWhenReleasesMoreCopiesThanItHas()
    {
        $bookId = Uuid::uuid1()->toString();
        $bookTitle = 'Bracia Karamazow';
        $receiveEvent = new BookChangeEvent(
            BookChangeEvent::RECEIVE,
            10,
            new DateTime('2019-01-01'),
            $bookId,
            $bookTitle
        );

        $bookChangeEventProvider = new InMemoryBookChangeEventProvider();
        $bookChangeEventProvider->addEvent($receiveEvent, $bookId);

        $receiveBookService = new ReleaseBookService($bookChangeEventProvider, new InMemoryBookChangeEventWriter());
        $copies = new Copies(11);
        $bookDTO = new BookDTO($bookId, $bookTitle, '');

        self::expectException(InvalidArgumentException::class);
        $receiveBookService->subtractCopiesForBook($copies, $bookDTO, $this->prepareReceiver(), '');
    }

    private function prepareReceiver(): ReceiverDTO
    {
        return new ReceiverDTO(Uuid::uuid1()->toString(), 'Niesiołowski Paweł');
    }
}
