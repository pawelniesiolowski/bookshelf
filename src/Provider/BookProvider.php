<?php

namespace App\Provider;

use App\Entity\Book;
use App\Repository\BookRepository;

class BookProvider
{
    private $bookRepository;

    public function __construct(BookRepository $bookRepository)
    {
        $this->bookRepository = $bookRepository;
    }

    public function findOne(int $id): Book
    {
        return $this->bookRepository->find($id);
    }

    public function getAllOrderedByAuthorAndTitle(): array
    {
        $books = $this->bookRepository->findAllOrderedByTitle();
        usort($books, 'strnatcasecmp');
        return $books;
    }
}

