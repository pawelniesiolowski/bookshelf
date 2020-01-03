<?php

namespace App\Catalog\Factory;

use App\Catalog\Persistence\Book;
use App\Shared\Tool\TextProcessor;

class BookFactory
{
    /**
     * @param string $json
     * @return Book
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
            $book->addAuthor($author);
        }
        return $book;
    }

    private function createPrice($price): float
    {
        $price = is_numeric($price) ? $price : 0;
        return (float)$price;
    }
}
