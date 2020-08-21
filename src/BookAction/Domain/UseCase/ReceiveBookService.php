<?php

namespace App\BookAction\Domain\UseCase;

use App\BookAction\Domain\Book\Book;
use App\BookAction\Domain\Book\Copies;
use App\BookAction\Domain\BookChangeEventProviderInterface;
use App\BookAction\Domain\BookChangeEventWriterInterface;
use App\Catalog\UseCase\BookDTO;

class ReceiveBookService
{
    private $bookChangeEventProvider;
    private $bookChangeEventWriter;

    public function __construct(
        BookChangeEventProviderInterface $bookChangeEventProvider,
        BookChangeEventWriterInterface $bookChangeEventWriter
    ) {
        $this->bookChangeEventProvider = $bookChangeEventProvider;
        $this->bookChangeEventWriter = $bookChangeEventWriter;
    }

    public function addCopiesForBook(Copies $copies, BookDTO $bookDTO): Copies
    {
        $events = $this->bookChangeEventProvider->getAllEventsForBook($bookDTO->id());
        $book = new Book($events, $bookDTO);
        $receiveEvent = $book->receive($copies);
        $this->bookChangeEventWriter->saveOne($receiveEvent);
        return $book->copies();
    }
}
