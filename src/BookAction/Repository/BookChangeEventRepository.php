<?php

namespace App\BookAction\Repository;

use App\BookAction\Persistence\BookChangeEvent;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

class BookChangeEventRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, BookChangeEvent::class);
    }

    public function findAllOrderedByDateDesc(): array
    {
        return $this->findBy([], ['date' => 'DESC']);
    }

    public function findAllByBookId(string $id): array
    {
        return $this->findBy(['bookId' => $id]);
    }
}
