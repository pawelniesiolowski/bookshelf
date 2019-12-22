<?php

namespace App\Catalog\Repository;

use App\Catalog\Persistence\Book;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

class BookRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Book::class);
    }

    public function findAllOrderedByTitle(): array
    {
        return $this->findBy(['deletedAt' => null], ['title' => 'ASC']);
    }
}
