<?php

namespace App\BookAction\Controller;

use App\BookAction\Domain\UseCase\ShowActionsForBookService;
use App\Catalog\UseCase\GetBookService;
use DateTime;
use Exception;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class IndexController extends AbstractController
{
    private $showActionsForBookService;
    private $getBookService;
    private $startDate;
    private $logger;

    public function __construct(
        ShowActionsForBookService $showActionsForBookService,
        GetBookService $getBookService,
        string $startDate,
        LoggerInterface $logger
    ) {
        $this->showActionsForBookService = $showActionsForBookService;
        $this->getBookService = $getBookService;
        $this->startDate = $startDate;
        $this->logger = $logger;
    }

    public function indexActionsForBook(string $bookId)
    {
        try {
            $bookDTO = $this->getBookService->byId($bookId);
            $startDate = new DateTime($this->startDate);
        } catch (Exception $e) {
            $this->logger->critical($e->getMessage());
            throw new NotFoundHttpException();
        }

        $eventsViews = $this->showActionsForBookService->showActionsForBookAfterDate($bookId, $startDate);

        return $this->render('book_action/index_for_book.html.twig', [
            'title' => $bookDTO->title(),
            'author' => $bookDTO->author(),
            'events' => $eventsViews,
        ]);
    }
}
