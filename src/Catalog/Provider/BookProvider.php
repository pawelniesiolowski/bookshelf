<?php

namespace App\Catalog\Provider;

use App\Catalog\Persistence\Book;
use App\Catalog\Repository\BookRepository;
use App\Catalog\Exception\BookException;

class BookProvider
{
    private $bookRepository;

    public function __construct(BookRepository $bookRepository)
    {
        $this->bookRepository = $bookRepository;
    }

    /**
     * @param string $id
     * @return Book
     * @throws BookException
     */
    public function findOne(string $id): Book
    {
        /** @var Book $book */
        $book = $this->bookRepository->find($id);
        if ($book === null) {
            throw new BookException('There is no book with id ' . $id . ' in database');
        }
        return $book;
    }

    public function getAllOrderedByAuthorAndTitle(): array
    {
        return $this->bookRepository->findAllOrderedByTitle();
    }
}
