<?php

namespace App\Provider;

use App\Repository\BookChangeEventRepository;

class BookChangeEventProvider
{
    private $bookChangeEventRepository;

    public function __construct(BookChangeEventRepository $bookChangeEventRepository)
    {
        $this->bookChangeEventRepository = $bookChangeEventRepository;
    }

    public function eventsOrderedByDateDesc(): array
    {
        $events = $this->bookChangeEventRepository->findAllOrderedByDateDesc();
        return $events;
    }
}

