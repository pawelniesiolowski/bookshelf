<?php

namespace App\BookAction\Infrastructure;

use App\BookAction\Domain\BookChangeEventView;
use App\BookAction\Domain\BookChangeEventWriterInterface;
use App\BookAction\Domain\BookChangeEvent;

class InMemoryBookChangeEventWriter implements BookChangeEventWriterInterface
{
    /**
     * @var BookChangeEvent[]
     */
    private $bookChangeEvents = [];

    public function saveOne(BookChangeEvent $bookChangeEvent): void
    {
        $this->bookChangeEvents = [$bookChangeEvent];
    }

    public function saveMany(array $bookChangeEvents): void
    {
        $this->bookChangeEvents = $bookChangeEvents;
    }

    /**
     * @return BookChangeEventView[]
     */
    public function getAllViews(): array
    {
        $bookChangeEventViews = [];
        foreach ($this->bookChangeEvents as $bookChangeEvent) {
            $bookChangeEventViews[] = $bookChangeEvent->toView();
        }
        return $bookChangeEventViews;
    }
}