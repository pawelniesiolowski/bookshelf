<?php

namespace App\Factory;

use App\Entity\Book;
use App\Provider\AuthorProvider;
use App\Entity\Author;

class BookFactory
{
    private $authorProvider;

    public function __construct(AuthorProvider $authorProvider)
    {
        $this->authorProvider = $authorProvider;
    }

    public function fromJson(string $json): Book
    {
        $data = json_decode($json, true);
        $book = new Book(
            $data['title'] ?? '',
            $data['ISBN'] ?? null,
            $data['price'] ?? null
        );
        foreach(($data['authors'] ?? []) as $author) {
            $book->addAuthor($this->getAuthor($author));
        }
        if (($data['copies'] ?? 0) > 0) {
            $book->receive($data['copies']);
        }
        return $book;
    }

    private function getAuthor(array $data): Author
    {
        $author = $this->authorProvider->findOneByNameAndSurname(
            $data['name'] ?? '',
            $data['surname'] ?? ''
        );
        if ($author === null) {
            $author = new Author($data['name'] ?? '', $data['surname'] ?? '');
        }
        return $author;
    }
}

