<?php

namespace App\Catalog\UseCase;

use App\Catalog\Repository\BookRepository;
use Doctrine\ORM\EntityManagerInterface;

class ActualizeBookCopiesService
{
    private $bookRepository;
    private $entityManager;

    public function __construct(BookRepository $bookRepository, EntityManagerInterface $entityManager)
    {
        $this->bookRepository = $bookRepository;
        $this->entityManager = $entityManager;
    }

    public function actualize(string $bookId, int $newNumberOfCopies): void
    {
        $book = $this->bookRepository->find($bookId);
        $book->setCopies($newNumberOfCopies);
        $this->entityManager->persist($book);
        $this->entityManager->flush();
    }
}
