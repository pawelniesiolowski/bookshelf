<?php

namespace App\Provider;

use App\Entity\Book;
use App\Repository\BookRepository;
use App\Exception\BookException;

class BookProvider
{
    private $bookRepository;

    public function __construct(BookRepository $bookRepository)
    {
        $this->bookRepository = $bookRepository;
    }

    public function findOne(int $id): Book
    {
        $book = $this->bookRepository->find($id);
        if ($book === null) {
            throw new BookException('There is no book with id ' . $id . ' in database');
        }
        return $book;
    }

    public function getAllOrderedByAuthorAndTitle(): array
    {
        $books = $this->bookRepository->findAllOrderedByTitle();
        usort($books, 'strnatcasecmp');
        return $books;
    }
}

