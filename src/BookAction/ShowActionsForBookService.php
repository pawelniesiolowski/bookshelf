<?php

namespace App\BookAction;

use App\BookAction\Repository\BookChangeEventRepository;
use DateTime;

class ShowActionsForBookService
{
    private $bookChangeEventRepository;

    public function __construct(BookChangeEventRepository $bookChangeEventRepository)
    {
        $this->bookChangeEventRepository = $bookChangeEventRepository;
    }

    public function showActionsForBookAfterDate(string $bookId, DateTime $date): array
    {
        return $this->bookChangeEventRepository->findAllByBookIdAfterDate($bookId, $date);
    }
}
