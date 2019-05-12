<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Repository\AuthorRepository;

class BookshelfController extends AbstractController
{
    public function index(AuthorRepository $authorRepository)
    {
        $authors = $authorRepository->getAllOrderBySurname();
        return $this->json(['authors' => $authors]);
    }
}

