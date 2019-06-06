<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Factory\BookFactory;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use App\Provider\BookProvider;
use App\Provider\AuthorProvider;
use App\Entity\Author;

class BookController extends AbstractController
{
    private $bookFactory;
    private $entityManager;
    private $bookProvider;
    private $authorProvider;

    public function __construct(
        BookFactory $bookFactory,
        EntityManagerInterface $entityManager,
        BookProvider $bookProvider,
        AuthorProvider $authorProvider
    ) {
        $this->bookFactory = $bookFactory;
        $this->entityManager = $entityManager;
        $this->bookProvider = $bookProvider;
        $this->authorProvider = $authorProvider;
    }

    public function new(Request $request)
    {
        $book = $this->bookFactory->fromJson($request->getContent());

        if (!$book->validate()) {
            return $this->json(['errors' => $book->getErrors()], 422);
        }

        $this->entityManager->persist($book);
        $this->entityManager->flush();
        
        return $this->json([], 201);
    }

    public function one(int $id)
    {
        return $this->json(['book' => $this->bookProvider->findOne($id)]);
    }

    public function delete(int $id)
    {
        $book = $this->bookProvider->findOne($id);
        $book->delete();
        $this->entityManager->persist($book);
        $this->entityManager->flush();
        return $this->json([], 200);
    }

    public function edit(int $id, Request $request)
    {
        $book = $this->bookProvider->findOne($id);

        $content = $request->getContent();
        $contentData = json_decode($content, true);
        $authors = [];
        foreach ($contentData['authors'] as $author) {
            $author = $this->authorProvider->findOneByNameAndSurname($author['name'] ?? '', $author['surname'] ?? '') ??
                new Author($author['name'] ?? '', $author['surname'] ?? '');
            $authors[] = $author;
        }

        $book->updateFromJson($content, $authors);

        if (!$book->validate()) {
            return $this->json(['errors' => $book->getErrors()], 422);
        }

        $this->entityManager->persist($book);
        $this->entityManager->flush();

        return $this->json([], 204);
    }
}

