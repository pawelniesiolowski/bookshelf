<?php

namespace App\Tests\BookAction\Domain\UseCase;

use App\BookAction\Domain\Book\Copies;
use App\BookAction\Domain\UseCase\ReceiveBookService;
use App\BookAction\Infrastructure\InMemoryBookChangeEventProvider;
use App\BookAction\Infrastructure\InMemoryBookChangeEventWriter;
use App\BookAction\Domain\BookChangeEvent;
use App\Catalog\UseCase\BookDTO;
use DateTime;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

class ReceiveBookServiceTest extends TestCase
{
    public function testItAddsCopiesForBook()
    {
        $bookId = Uuid::uuid1()->toString();
        $bookTitle = 'Bracia Karamazow';
        $bookAuthor = 'Fiodor Dostojewski';

        $bookChangeEvent = new BookChangeEvent(
            BookChangeEvent::RECEIVE,
            5,
            new DateTime('2020-04-17'),
            $bookId,
            $bookTitle,
            $bookAuthor
        );

        $bookChangeEventProvider = new InMemoryBookChangeEventProvider();
        $bookChangeEventProvider->addEvent($bookChangeEvent, $bookId);

        $receiveBookService = new ReceiveBookService($bookChangeEventProvider, new InMemoryBookChangeEventWriter());
        $copies = new Copies(5);
        $bookDTO = new BookDTO($bookId, $bookTitle, $bookAuthor);

        $newNumberOfBookCopies = $receiveBookService->addCopiesForBook($copies, $bookDTO);

        $this->assertSame(10, $newNumberOfBookCopies->toInt());
    }

    public function testItAddsCopiesForBookWithZeroCopies()
    {
        $bookId = Uuid::uuid1()->toString();
        $bookTitle = 'Bracia Karamazow';
        $bookAuthor = 'Fiodor Dostojewski';

        $bookChangeEventProvider = new InMemoryBookChangeEventProvider();

        $receiveBookService = new ReceiveBookService($bookChangeEventProvider, new InMemoryBookChangeEventWriter());
        $copies = new Copies(5);
        $bookDTO = new BookDTO($bookId, $bookTitle, $bookAuthor);

        $newNumberOfBookCopies = $receiveBookService->addCopiesForBook($copies, $bookDTO);

        $this->assertSame(5, $newNumberOfBookCopies->toInt());
    }
}