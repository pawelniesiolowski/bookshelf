<?php

namespace App\BookAction\Controller;

use App\BookAction\Domain\Book\Copies;
use App\BookAction\Domain\UseCase\ReleaseBookService;
use App\Catalog\Exception\BookNotFoundException;
use App\Catalog\UseCase\ActualizeBookCopiesService;
use App\Catalog\UseCase\GetBookService;
use App\Receiver\Exception\ReceiverNotFoundException;
use App\Receiver\UseCase\GetReceiverService;
use DomainException;
use InvalidArgumentException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class ReleaseBookController extends AbstractController
{
    private $releaseBookService;
    private $getBookService;
    private $getReceiverService;
    private $actualizeBookCopiesService;

    public function __construct(
        ReleaseBookService $releaseBookService,
        GetBookService $getBookService,
        GetReceiverService $getReceiverService,
        ActualizeBookCopiesService $actualizeBookCopiesService
    ) {

        $this->releaseBookService = $releaseBookService;
        $this->getBookService = $getBookService;
        $this->getReceiverService = $getReceiverService;
        $this->actualizeBookCopiesService = $actualizeBookCopiesService;
    }

    public function release(string $id, Request $request)
    {
        try {
            $bookDTO = $this->getBookService->byId($id);
            $data = json_decode($request->getContent(), true);
            $receiverDTO = $this->getReceiverService->byId($data['receiver'] ?? '');
            $newNumberOfCopies = $this->releaseBookService->subtractCopiesForBook(
                new Copies(intval($data['copies'])),
                $bookDTO,
                $receiverDTO,
                $data['comment'] ?? ''
            );
        } catch (BookNotFoundException $e) {
            $errors['book'] = 'Wybrana książka nie istnieje w bazie';
            return $this->json(['errors' => $errors], 422);
        } catch (ReceiverNotFoundException $e) {
            $errors['receiver'] = 'Wybierz osobę, która jest uprawniona do pobrania książek';
            return $this->json(['errors' => $errors], 422);
        } catch (InvalidArgumentException | DomainException $e) {
            $errors = ['copies' => 'Ilość egzemplarzy musi być liczbą większą od zera'];
            return $this->json(['errors' => $errors], 422);
        }
        $this->actualizeBookCopiesService->actualize($id, $newNumberOfCopies->toInt());
        return $this->json([], 204);
    }
}