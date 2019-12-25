<?php

namespace App\Catalog\Factory;

use App\Catalog\Persistence\Book;
use App\Catalog\Provider\AuthorProvider;
use App\Catalog\Persistence\Author;
use App\Shared\Tool\TextProcessor;
use Doctrine\ORM\NonUniqueResultException;

class BookFactory
{
    private $authorProvider;

    public function __construct(AuthorProvider $authorProvider)
    {
        $this->authorProvider = $authorProvider;
    }

    /**
     * @param string $json
     * @return Book
     * @throws NonUniqueResultException
     */
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
        return $book;
    }

    private function createPrice($price): float
    {
        $price = is_numeric($price) ? $price : 0;
        return (float)$price;
    }

    /**
     * @param array $data
     * @return Author
     * @throws NonUniqueResultException
     */
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
