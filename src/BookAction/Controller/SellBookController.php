<?php

namespace App\BookAction\Controller;

use App\BookAction\Domain\Book\Copies;
use App\BookAction\Domain\UseCase\SellBookService;
use App\Catalog\Exception\BookNotFoundException;
use App\Catalog\UseCase\ActualizeBookCopiesService;
use App\Catalog\UseCase\GetBookService;
use DomainException;
use InvalidArgumentException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class SellBookController extends AbstractController
{
    private $sellBookService;
    private $getBookService;
    private $actualizeBookCopiesService;

    public function __construct(
        SellBookService $sellBookService,
        GetBookService $getBookService,
        ActualizeBookCopiesService $actualizeBookCopiesService
    ) {
        $this->sellBookService = $sellBookService;
        $this->getBookService = $getBookService;
        $this->actualizeBookCopiesService = $actualizeBookCopiesService;
    }

    public function sell(string $id, Request $request)
    {
        try {
            $bookDTO = $this->getBookService->byId($id);
            $data = json_decode($request->getContent(), true);
            $newNumberOfCopies = $this->sellBookService->subtractCopiesForBook(
                new Copies(intval($data['copies'])),
                $bookDTO,
                $data['comment'] ?? ''
            );
        } catch (BookNotFoundException $e) {
            $errors['book'] = 'Wybrana książka nie istnieje w bazie';
            return $this->json(['errors' => $errors], 422);
        } catch (InvalidArgumentException | DomainException $e) {
            $errors = ['copies' => 'Ilość egzemplarzy musi być liczbą większą od zera'];
            return $this->json(['errors' => $errors], 422);
        }
        $this->actualizeBookCopiesService->actualize($id, $newNumberOfCopies->toInt());
        return $this->json([], 204);
    }
}
