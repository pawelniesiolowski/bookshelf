<?php

namespace App\BookAction\Domain\UseCase;

use App\BookAction\Domain\Book\Book;
use App\BookAction\Domain\Book\Copies;
use App\BookAction\Domain\BookChangeEventProviderInterface;
use App\BookAction\Domain\BookChangeEventWriterInterface;
use App\Catalog\UseCase\BookDTO;

class SellBookService
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

    public function subtractCopiesForBook(
        Copies $copies,
        BookDTO $bookDTO,
        string $comment
    ): Copies {
        $events = $this->bookChangeEventProvider->getAllEventsForBook($bookDTO->id());
        $book = new Book($events, $bookDTO);
        $releaseEvent = $book->sell($copies, $comment);
        $this->bookChangeEventWriter->saveOne($releaseEvent);
        return $book->copies();
    }
}