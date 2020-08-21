<?php

namespace App\BookAction\Domain;

use DateTime;

interface BookChangeEventProviderInterface
{
    /**
     * @param string $bookBookId
     * @return BookChangeEvent[]
     */
    public function getAllEventsForBook(string $bookBookId): array;

    /**
     * @param string $bookId
     * @param DateTime $date
     * @return BookChangeEvent[]
     */
    public function getEventsForBookAfterDate(string $bookId, DateTime $date): array;
}