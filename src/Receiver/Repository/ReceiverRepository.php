<?php

namespace App\Receiver\Repository;

use App\Receiver\Persistence\Receiver;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Symfony\Bridge\Doctrine\RegistryInterface;

class ReceiverRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Receiver::class);
    }

    /**
     * @param int $id
     * @return Receiver
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function findOneById(int $id): Receiver
    {
        return $this->createQueryBuilder('receiver')
            ->andWhere('receiver.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getSingleResult();
    }

    public function findAllNonDeletedOrderAlfabethically(): array
    {
        return $this->findBy(['deletedAt' => null], ['surname' => 'ASC', 'name' => 'ASC']);
    }
}
