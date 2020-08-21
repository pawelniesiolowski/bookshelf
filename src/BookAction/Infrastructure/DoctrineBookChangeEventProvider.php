<?php

namespace App\BookAction\Infrastructure;

use App\BookAction\Domain\BookChangeEventProviderInterface;
use App\BookAction\Infrastructure\Repository\BookChangeEventRepository;
use DateTime;

class DoctrineBookChangeEventProvider implements BookChangeEventProviderInterface
{
    private $bookChangeEventRepository;

    public function __construct(BookChangeEventRepository $bookChangeEventRepository)
    {
        $this->bookChangeEventRepository = $bookChangeEventRepository;
    }

    public function getAllEventsForBook(string $bookId): array
    {
        return $this->bookChangeEventRepository->findAllByBookId($bookId);
    }

    public function getEventsForBookAfterDate(string $bookId, DateTime $date): array
    {
        return $this->bookChangeEventRepository->findAllByBookIdAfterDate($bookId, $date);
    }
}