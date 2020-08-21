<?php

namespace App\BookAction\Controller;

use App\BookAction\Domain\UseCase\ShowActionsForBookService;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class IndexController extends AbstractController
{
    private $showActionsForBookService;

    public function __construct(ShowActionsForBookService $showActionsForBookService)
    {
        $this->showActionsForBookService = $showActionsForBookService;
    }

    public function indexActionsForBook(string $bookId)
    {
        $afterDate = new DateTime('2019-01-01');
        $events = $this->showActionsForBookService->showActionsForBookAfterDate($bookId, $afterDate);
        return $this->render('book_action/index_for_book.html.twig', ['events' => $events]);
    }
}
