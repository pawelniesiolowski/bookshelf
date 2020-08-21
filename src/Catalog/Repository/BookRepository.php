<?php

namespace App\Catalog\Repository;

use App\Catalog\Model\Book;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class BookRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Book::class);
    }

    public function findAllOrderedByTitle(): array
    {
        return $this->findBy(['deletedAt' => null], ['title' => 'ASC']);
    }
}
