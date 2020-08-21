<?php

namespace App\Receiver\Repository;

use App\Receiver\Model\Receiver;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException as NonUniqueResultExceptionAlias;
use Doctrine\ORM\NoResultException;
use Doctrine\Persistence\ManagerRegistry;

class ReceiverRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Receiver::class);
    }

    /**
     * @param string $id
     * @return Receiver
     * @throws NoResultException
     * @throws NonUniqueResultExceptionAlias
     */
    public function findOneById(string $id): Receiver
    {
        return $this->createQueryBuilder('receiver')
            ->andWhere('receiver.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getSingleResult();
    }

    public function findAllNonDeletedOrderedAlphabetically(): array
    {
        return $this->findBy(['deletedAt' => null], ['surname' => 'ASC', 'name' => 'ASC']);
    }
}
