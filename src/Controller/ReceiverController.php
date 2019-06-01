<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use App\Factory\ReceiverFactory;

class ReceiverController extends AbstractController
{
    private $receiverFactory;
    private $entityManager;

    public function __construct(
        ReceiverFactory $receiverFactory,
        EntityManagerInterface $entityManager
    ) {
        $this->receiverFactory = $receiverFactory;
        $this->entityManager = $entityManager;
    }

    public function new(Request $request)
    {
        $receiver = $this->receiverFactory->fromJson($request->getContent());
        if (!$receiver->validate()) {
            return $this->json(['errors' => $receiver->getErrors()], 422);
        }
        $this->entityManager->persist($receiver);
        $this->entityManager->flush();
        return $this->json([], 201);
    }
}

