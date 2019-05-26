<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use App\Exception\BookException;
use App\Provider\BookProvider;
use Doctrine\ORM\EntityManagerInterface;

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
            return $this->json(['errors' => ['copies' => 'Egzemplarzy musi być więcej niż 0']], 422);
        }

        $this->entityManager->persist($book);
        $this->entityManager->flush();

        return $this->json([], 204);
    }
}

