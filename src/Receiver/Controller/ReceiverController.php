<?php

namespace App\Receiver\Controller;

use App\Receiver\Repository\ReceiverRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use App\Receiver\Factory\ReceiverFactory;

class ReceiverController extends AbstractController
{
    private $receiverFactory;
    private $entityManager;
    private $receiverRepository;

    public function __construct(
        ReceiverFactory $receiverFactory,
        EntityManagerInterface $entityManager,
        ReceiverRepository $receiverRepository
    ) {
        $this->receiverFactory = $receiverFactory;
        $this->entityManager = $entityManager;
        $this->receiverRepository = $receiverRepository;
    }

    public function show()
    {
        return $this->render('receiver/receivers.html.twig');
    }

    public function index()
    {
        return $this->json(['receivers' => $this->receiverRepository->findAllNonDeletedOrderedAlphabetically()]);
    }

    public function one(string $id)
    {
        return $this->json(['receiver' => $this->receiverRepository->find($id)]);
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

    public function edit(string $id, Request $request)
    {
        $receiver = $this->receiverRepository->find($id);
        $receiver->editFromJsonData($request->getContent());
        if (!$receiver->validate()) {
            return $this->json(['errors' => $receiver->getErrors()], 422);
        }
        $this->entityManager->persist($receiver);
        $this->entityManager->flush();
        return $this->json([], 204);
    }

    public function delete(string $id)
    {
        $receiver = $this->receiverRepository->find($id);
        $receiver->delete();
        $this->entityManager->persist($receiver);
        $this->entityManager->flush();
        return $this->json([], 200);
    }
}
