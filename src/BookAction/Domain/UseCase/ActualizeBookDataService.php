<?php

namespace App\BookAction\Domain\UseCase;

use App\BookAction\Domain\BookChangeEventProviderInterface;
use App\BookAction\Domain\BookChangeEventWriterInterface;
use App\Catalog\UseCase\BookDTO;

class ActualizeBookDataService
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

    public function actualizeDataForBook(BookDTO $bookDTO): bool
    {
        $events = $this->bookChangeEventProvider->getAllEventsForBook($bookDTO->id());
        foreach ($events as $event) {
            $event->setBookTitle($bookDTO->title());
            $event->setBookAuthor($bookDTO->author());
        }
        $this->bookChangeEventWriter->saveMany($events);
        return true;
    }
}
