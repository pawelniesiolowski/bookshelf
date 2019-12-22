<?php

namespace App\Catalog\Repository;

use App\Catalog\Persistence\Author;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Bridge\Doctrine\RegistryInterface;

class AuthorRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Author::class);
    }

    public function getAllOrderBySurname(): array
    {
        return $this->findBy([], ['surname' => 'ASC']);
    }

    /**
     * @param string $name
     * @param string $surname
     * @return Author|null
     * @throws NonUniqueResultException
     */
    public function findOneByNameAndSurname(string $name, string $surname): ?Author
    {
        return $this->createQueryBuilder('author')
            ->andWhere('author.name = :name')
            ->andWhere('author.surname = :surname')
            ->setParameter('name', $name)
            ->setParameter('surname', $surname)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
