<?php

namespace App\Provider;

use App\Repository\AuthorRepository;

class AuthorProvider
{
    private $authorRepository;

    public function __construct(AuthorRepository $authorRepository)
    {
        $this->authorRepository = $authorRepository;
    }

    public function all()
    {
        return $this->authorRepository->getAllOrderBySurname();
    }
}

