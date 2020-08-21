<?php

namespace App\BookAction\Infrastructure;

use App\BookAction\Domain\BookChangeEvent;
use App\BookAction\Domain\BookChangeEventProviderInterface;
use BadMethodCallException;
use DateTime;

class InMemoryBookChangeEventProvider implements BookChangeEventProviderInterface
{
    private $events = [];

    public function addEvent(BookChangeEvent $bookChangeEvent, string $bookId): void
    {
        $this->events[$bookId][] = $bookChangeEvent;
    }

    public function getAllEventsForBook(string $bookId): array
    {
        return $this->events[$bookId] ?? [];
    }

    public function getEventsForBookAfterDate(string $bookId, DateTime $date): array
    {
        $message = 'Use case test do not check event date. ' .
            'This functionality is tested on repository level with real database';
        throw new BadMethodCallException($message);
    }
}