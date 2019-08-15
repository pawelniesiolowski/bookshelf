<?php

namespace App\Factory;

use App\Entity\Book;
use App\Provider\AuthorProvider;
use App\Entity\Author;
use App\Tool\TextProcessor;

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
        if (is_array($data)) {
            $data = TextProcessor::trimData($data);
        }
        $book = new Book(
            $data['title'] ?? ''
        );
        if (!empty($data['price'])) {
            $book->setPrice($this->createPrice($data['price']));
        }
        if (!empty($data['ISBN'])) {
            $book->setISBN($data['ISBN']);
        }
        foreach(($data['authors'] ?? []) as $author) {
            $book->addAuthor($this->getAuthor($author));
        }
        if (($data['copies'] ?? 0) > 0) {
            $book->receive($data['copies']);
        }
        return $book;
    }

    private function createPrice($price): float
    {
        $price = is_numeric($price) ? $price : 0;
        return (float)$price;
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

