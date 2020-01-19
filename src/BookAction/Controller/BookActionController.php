<?php

namespace App\BookAction\Controller;

use App\BookAction\ShowActionsForBookService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class BookActionController extends AbstractController
{
    private $showActionsForBookService;

    public function __construct(ShowActionsForBookService $showActionsForBookService)
    {
        $this->showActionsForBookService = $showActionsForBookService;
    }

    public function indexActionsForBook(string $bookId)
    {
        $date = new \DateTime('2019-01-01');
        $events = $this->showActionsForBookService->showActionsForBookAfterDate($bookId, $date);
        return $this->render('book_action/index_for_book.html.twig', ['events' => $events]);
    }
}
