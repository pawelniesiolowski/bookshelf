<?php

namespace App\Catalog\Controller;

use App\BookAction\Domain\Book\Copies;
use App\BookAction\Domain\UseCase\ActualizeBookDataService;
use App\BookAction\Domain\UseCase\ReceiveBookService;
use App\Catalog\Factory\BookFactory;
use App\Catalog\Repository\BookRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class BookController extends AbstractController
{
    private $bookFactory;
    private $entityManager;
    private $receiveBookService;
    private $bookRepository;

    public function __construct(
        BookFactory $bookFactory,
        EntityManagerInterface $entityManager,
        ReceiveBookService $receiveBookService,
        BookRepository $bookRepository
    ) {
        $this->bookFactory = $bookFactory;
        $this->entityManager = $entityManager;
        $this->receiveBookService = $receiveBookService;
        $this->bookRepository = $bookRepository;
    }

    public function show()
    {
        return $this->render('catalog/index.html.twig');
    }

    public function index()
    {
        $books = $this->bookRepository->findAllOrderedByTitle();
        return $this->json(['books' => $books]);
    }

    public function one(string $id)
    {
        $book = $this->bookRepository->find($id);
        if ($book === null) {
            throw new NotFoundHttpException();
        }
        return $this->json(['book' => $book]);
    }

    public function form()
    {
        return $this->render('catalog/book_form.twig');
    }

    public function new(Request $request)
    {
        $book = $this->bookFactory->fromJson($request->getContent());

        if (!$book->validate()) {
            return $this->json(['errors' => $book->getErrors()], 422);
        }

        $this->entityManager->persist($book);
        $this->entityManager->flush();

        $data = json_decode($request->getContent(), true);
        if (!empty($data['copies']) && $data['copies'] > 0) {
            $this->receiveBookService->addCopiesForBook(new Copies(intval($data['copies'])), $book->toDTO());
        }

        return $this->json([], 201);
    }

    public function edit(string $id, Request $request, ActualizeBookDataService $actualizeBookDataService)
    {
        $book = $this->bookRepository->find($id);

        $content = $request->getContent();
        $contentData = json_decode($content, true);

        $book->updateFromJson($content, $contentData['authors']);

        if (!$book->validate()) {
            return $this->json(['errors' => $book->getErrors()], 422);
        }

        $this->entityManager->beginTransaction();

        $this->entityManager->persist($book);
        $this->entityManager->flush();

        if (!$actualizeBookDataService->actualizeDataForBook($book->toDTO())) {
            $this->entityManager->rollback();
            $errors['title'] = 'Nie udało się zmienić danych książki. Spróbuj ponownie później';
            return $this->json(['errors' => $errors], 500);
        }

        $this->entityManager->commit();

        return $this->json([], 204);
    }

    public function delete(string $id)
    {
        $book = $this->bookRepository->find($id);
        $book->delete();
        $this->entityManager->persist($book);
        $this->entityManager->flush();
        return $this->json([], 200);
    }
}
