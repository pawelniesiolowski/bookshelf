<?php

namespace App\Catalog\UseCase;

use App\Catalog\Exception\BookNotFoundException;
use App\Catalog\Repository\BookRepository;

class GetBookService
{
    private $bookRepository;

    public function __construct(BookRepository $bookRepository)
    {
        $this->bookRepository = $bookRepository;
    }

    /**
     * @param string $bookId
     * @return BookDTO
     * @throws BookNotFoundException
     */
    public function byId(string $bookId): BookDTO
    {
        $book = $this->bookRepository->find($bookId);
        if ($book === null) {
            throw new BookNotFoundException('There is no book with id ' . $bookId);
        }
        return $book->toDTO();
    }
}
