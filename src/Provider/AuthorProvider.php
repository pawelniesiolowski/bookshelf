<?php

namespace App\Provider;

use App\Repository\AuthorRepository;
use App\Entity\Author;

class AuthorProvider
{
    private $authorRepository;

    public function __construct(AuthorRepository $authorRepository)
    {
        $this->authorRepository = $authorRepository;
    }

    public function all(): array
    {
        return $this->authorRepository->getAllOrderBySurname();
    }

    public function findOneByNameAndSurname(string $name, string $surname): ?Author
    {
        return $this->authorRepository->findOneByNameAndSurname($name, $surname);
    }
}

