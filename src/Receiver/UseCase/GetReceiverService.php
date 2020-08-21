<?php

namespace App\Receiver\UseCase;

use App\Receiver\Exception\ReceiverNotFoundException;
use App\Receiver\Repository\ReceiverRepository;

class GetReceiverService
{
    private $receiverRepository;

    public function __construct(ReceiverRepository $receiverRepository)
    {
        $this->receiverRepository = $receiverRepository;
    }

    /**
     * @param string $receiverId
     * @return ReceiverDTO
     * @throws ReceiverNotFoundException
     */
    public function byId(string $receiverId): ReceiverDTO
    {
       $receiver = $this->receiverRepository->find($receiverId);
       if ($receiver === null) {
           throw new ReceiverNotFoundException('There is no receiver with id ' . $receiverId);
       }
       return $receiver->toDTO();
    }
}