<?php

namespace App\Catalog\Controller;

use App\Catalog\Exception\BookException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Catalog\Factory\BookFactory;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use App\Catalog\Provider\BookProvider;
use App\Catalog\Provider\AuthorProvider;
use App\Catalog\Persistence\Author;

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
