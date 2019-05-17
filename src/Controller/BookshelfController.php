<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Provider\AuthorProvider;

class BookshelfController extends AbstractController
{
    public function index(AuthorProvider $authorProvider)
    {
        $authors = $authorProvider->all();
        return $this->json(['authors' => $authors]);
    }
}

