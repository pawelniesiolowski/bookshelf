<?php

namespace App\Catalog\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Catalog\Factory\BookFactory;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use App\Catalog\Provider\BookProvider;

class BookController extends AbstractController
{
    private $bookFactory;
    private $entityManager;
    private $bookProvider;

    public function __construct(
        BookFactory $bookFactory,
        EntityManagerInterface $entityManager,
        BookProvider $bookProvider
    ) {
        $this->bookFactory = $bookFactory;
        $this->entityManager = $entityManager;
        $this->bookProvider = $bookProvider;
    }

    public function new(Request $request)
    {
        $book = $this->bookFactory->fromJson($request->getContent());

        if (!$book->validate()) {
            return $this->json(['errors' => $book->getErrors()], 422);
        }

        $this->entityManager->persist($book);
        $this->entityManager->flush();

        // Temporary mixing responsibilities of two different subdomains
        $data = json_decode($request->getContent(), true);
        if (!empty($data['copies']) && $data['copies'] > 0) {
            $event = $book->receive($data['copies']);
            $this->entityManager->persist($book);
            $this->entityManager->persist($event);
            $this->entityManager->flush();
        }

        return $this->json([], 201);
    }

    public function one(string $id)
    {
        return $this->json(['book' => $this->bookProvider->findOne($id)]);
    }

    public function delete(string $id)
    {
        $book = $this->bookProvider->findOne($id);
        $book->delete();
        $this->entityManager->persist($book);
        $this->entityManager->flush();
        return $this->json([], 200);
    }

    public function edit(string $id, Request $request)
    {
        $book = $this->bookProvider->findOne($id);

        $content = $request->getContent();
        $contentData = json_decode($content, true);

        $book->updateFromJson($content, $contentData['authors']);

        if (!$book->validate()) {
            return $this->json(['errors' => $book->getErrors()], 422);
        }

        $this->entityManager->persist($book);
        $this->entityManager->flush();

        return $this->json([], 204);
    }
}
