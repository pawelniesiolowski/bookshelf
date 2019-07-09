<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use App\Exception\BookException;
use App\Exception\BookChangeEventException;
use App\Provider\BookProvider;
use Doctrine\ORM\EntityManagerInterface;
use App\Provider\ReceiverProvider;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\NonUniqueResultException;

class BookshelfController extends AbstractController
{
    private $bookProvider;
    private $entityManager;

    public function __construct(
        BookProvider $bookProvider,
        EntityManagerInterface $entityManager
    ) {
        $this->bookProvider = $bookProvider;
        $this->entityManager = $entityManager;
    }

    public function index()
    {
        $books = $this->bookProvider->getAllOrderedByAuthorAndTitle();
        return $this->json(['books' => $books]);
    }

    public function receive(int $id, Request $request)
    {
        $book = $this->bookProvider->findOne($id);
        $data = json_decode($request->getContent(), true);

        try {
            $book->receive($data['copies']);
        } catch (BookException $e) {
            return $this->json(['errors' => ['copies' => $e->getMessage()]], 422);
        }

        $this->entityManager->persist($book);
        $this->entityManager->flush();

        return $this->json([], 204);
    }

    public function release(int $id, Request $request, ReceiverProvider $receiverProvider)
    {
        $book = $this->bookProvider->findOne($id);
        $data = json_decode($request->getContent(), true);
        $errors = [];
        try {
            $receiver = $receiverProvider->findOneById($data['receiverId']);
            $book->release($data['copies'], $receiver, $data['comment']);
        } catch (NonUniqueResultException | NoResultException $e) {
            $errors['receiverId'] = 'Wybierz osobę, która jest uprawniona do pobrania książek';
        } catch (BookException $e) {
            $errors['copies'] = $e->getMessage();
        }

        if (count($errors) > 0) {
            return $this->json(['errors' => $errors], 422);
        }
        
        $this->entityManager->persist($book);
        $this->entityManager->flush();

        return $this->json([], 204);
    }

    public function sell(int $id, Request $request)
    {
        $book = $this->bookProvider->findOne($id);
        $data = json_decode($request->getContent(), true);
        $errors = [];
        try {
            $book->sell($data['copies']);
        } catch (BookException $e) {
            $errors['copies'] = $e->getMessage();
        }

        if (count($errors) > 0) {
            return $this->json(['errors' => $errors], 422);
        }

        $this->entityManager->persist($book);
        $this->entityManager->flush();

        return $this->json([], 204);
    }
}

