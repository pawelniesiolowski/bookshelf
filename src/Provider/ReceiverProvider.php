<?php

namespace App\Provider;

use App\Repository\ReceiverRepository;
use App\Entity\Receiver;

class ReceiverProvider
{
    private $receiverRepository;

    public function __construct(ReceiverRepository $receiverRepository)
    {
        $this->receiverRepository = $receiverRepository;
    }

    public function findOneById(int $id): Receiver
    {
        return $this->receiverRepository->findOneById($id);
    }

    public function findAll(): array
    {
        return $this->receiverRepository->findAllNonDeletedOrderAlfabethically();
    }
}

