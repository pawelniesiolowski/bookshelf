<?php

namespace App\Tests\BookAction\Domain\UseCase;

use App\BookAction\Domain\Book\Copies;
use App\BookAction\Domain\BookChangeEvent;
use App\BookAction\Domain\UseCase\SellBookService;
use App\BookAction\Infrastructure\InMemoryBookChangeEventProvider;
use App\BookAction\Infrastructure\InMemoryBookChangeEventWriter;
use App\Catalog\UseCase\BookDTO;
use DateTime;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

class SellBookServiceTest extends TestCase
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

        $sellBookService = new SellBookService(
            $bookChangeEventProvider,
            $bookChangeEventWriter
        );

        $copies = new Copies(4);
        $bookDTO = new BookDTO($bookId, $bookTitle, '');

        $newNumberOfBookCopies = $sellBookService->subtractCopiesForBook($copies, $bookDTO, '');
        self::assertSame(6, $newNumberOfBookCopies->toInt());
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

        $sellBookService = new SellBookService($bookChangeEventProvider, new InMemoryBookChangeEventWriter());
        $copies = new Copies(11);
        $bookDTO = new BookDTO($bookId, $bookTitle, '');

        self::expectException(InvalidArgumentException::class);
        $sellBookService->subtractCopiesForBook($copies, $bookDTO, '');
    }
}