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
        } catch (\TypeError $e) {
            return $this->json(['errors' => ['copies' => 'Ilość egzemplarzy musi być liczbą większą od zera']], 422);
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
            $receiver = $receiverProvider->findOneById($data['receiver']);
        } catch (\TypeError | NonUniqueResultException | NoResultException $e) {
            $errors['receiver'] = 'Wybierz osobę, która jest uprawniona do pobrania książek';
            return $this->json(['errors' => $errors], 422);
        }

        try {
            $book->release($data['copies'], $receiver, $data['comment']);
        } catch (BookException $e) {
            $errors['copies'] = $e->getMessage();
            return $this->json(['errors' => $errors], 422);
        } catch (\TypeError $e) {
            $errors['copies'] = 'Ilość egzemplarzy musi być liczbą większą od zera';
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
        } catch (\TypeError $e) {
            return $this->json(['errors' => ['copies' => 'Ilość egzemplarzy musi być liczbą większą od zera']], 422);
        }

        if (count($errors) > 0) {
            return $this->json(['errors' => $errors], 422);
        }

        $this->entityManager->persist($book);
        $this->entityManager->flush();

        return $this->json([], 204);
    }
}

