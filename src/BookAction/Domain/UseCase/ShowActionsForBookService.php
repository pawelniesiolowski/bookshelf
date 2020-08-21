<?php

namespace App\BookAction\Domain\UseCase;

use App\BookAction\Domain\BookChangeEventProviderInterface;
use App\BookAction\Domain\BookChangeEventView;
use DateTime;

class ShowActionsForBookService
{
    private $bookChangeEventProvider;

    public function __construct(BookChangeEventProviderInterface $bookChangeEventProvider)
    {
        $this->bookChangeEventProvider = $bookChangeEventProvider;
    }

    /**
     * @param string $bookId
     * @param DateTime $date
     * @return BookChangeEventView[]
     */
    public function showActionsForBookAfterDate(string $bookId, DateTime $date): array
    {
        $events = $this->bookChangeEventProvider->getEventsForBookAfterDate($bookId, $date);
        $eventsViews = [];
        foreach ($events as $event) {
            $eventsViews[] = $event->toView();
        }
        return $eventsViews;
    }
}
