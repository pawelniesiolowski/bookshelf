<?php

namespace App\Receiver\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use App\Factory\ReceiverFactory;
use App\Provider\ReceiverProvider;

class ReceiverController extends AbstractController
{
    private $receiverFactory;
    private $entityManager;
    private $receiverProvider;

    public function __construct(
        ReceiverFactory $receiverFactory,
        EntityManagerInterface $entityManager,
        ReceiverProvider $receiverProvider
    ) {
        $this->receiverFactory = $receiverFactory;
        $this->entityManager = $entityManager;
        $this->receiverProvider = $receiverProvider;
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

    public function index()
    {
        return $this->json(['receivers' => $this->receiverProvider->findAll()]);
    }

    public function one(int $id)
    {
        return $this->json(['receiver' => $this->receiverProvider->findOneById($id)]);
    }

    public function delete(int $id)
    {
        $receiver = $this->receiverProvider->findOneById($id);
        $receiver->delete();
        $this->entityManager->persist($receiver);
        $this->entityManager->flush();
        return $this->json([], 200);
    }

    public function edit(int $id, Request $request)
    {
        $receiver = $this->receiverProvider->findOneById($id);
        $receiver->editFromJsonData($request->getContent());
        if (!$receiver->validate()) {
            return $this->json(['errors' => $receiver->getErrors()], 422);
        }
        $this->entityManager->persist($receiver);
        $this->entityManager->flush();
        return $this->json([], 204);
    }
}
