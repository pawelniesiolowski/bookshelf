<?php

namespace App\Receiver\Provider;

use App\Receiver\Repository\ReceiverRepository;
use App\Receiver\Persistence\Receiver;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;

class ReceiverProvider
{
    private $receiverRepository;

    public function __construct(ReceiverRepository $receiverRepository)
    {
        $this->receiverRepository = $receiverRepository;
    }

    /**
     * @param int $id
     * @return Receiver
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function findOneById(int $id): Receiver
    {
        return $this->receiverRepository->findOneById($id);
    }

    public function findAll(): array
    {
        return $this->receiverRepository->findAllNonDeletedOrderAlfabethically();
    }
}
