<?php

namespace App\BookAction\Controller;

use App\Catalog\UseCase\ActualizeBookCopiesService;
use App\Catalog\UseCase\GetBookService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use App\BookAction\Domain\Book\Copies;
use App\BookAction\Domain\UseCase\ReceiveBookService;

class ReceiveBookController extends AbstractController
{
    private $receiveBookService;
    private $getBookService;
    private $actualizeBookCopiesService;

    public function __construct(
        ReceiveBookService $receiveBookService,
        GetBookService $getBookService,
        ActualizeBookCopiesService $actualizeBookCopiesService
    ) {
        $this->receiveBookService = $receiveBookService;
        $this->getBookService = $getBookService;
        $this->actualizeBookCopiesService = $actualizeBookCopiesService;
    }

    public function receive(string $id, Request $request)
    {
        $data = json_decode($request->getContent(), true);
        $bookDTO = $this->getBookService->byId($id);

        try {
            $newNumberOfBookCopies = $this->receiveBookService->addCopiesForBook(
                new Copies(intval($data['copies'])),
                $bookDTO
            );
        } catch (\InvalidArgumentException | \DomainException $e) {
            return $this->json(['errors' => ['copies' => 'Ilość egzemplarzy musi być liczbą większą od zera']], 422);
        }

        $this->actualizeBookCopiesService->actualize($id, $newNumberOfBookCopies->toInt());

        return $this->json([], 204);
    }
}
