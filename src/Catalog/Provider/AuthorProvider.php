<?php

namespace App\Catalog\Provider;

use App\Catalog\Repository\AuthorRepository;
use App\Catalog\Persistence\Author;
use Doctrine\ORM\NonUniqueResultException;

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

    /**
     * @param string $name
     * @param string $surname
     * @return Author|null
     * @throws NonUniqueResultException
     */
    public function findOneByNameAndSurname(string $name, string $surname): ?Author
    {
        return $this->authorRepository->findOneByNameAndSurname($name, $surname);
    }
}
