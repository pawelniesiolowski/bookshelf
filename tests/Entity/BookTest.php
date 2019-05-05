<?php

namespace App\Tests\Entity;

use PHPUnit\Framework\TestCase;
use App\Entity\Book;
use App\Entity\Author;

class BookTest extends TestCase
{
    public function testNewBookCreation()
    {
        $book = new Book('Bracia Karamazow', 1, '0123456789', 29.99);
        $book->addAuthor(new Author('Fiodor', 'Dostojewski'));
        $this->assertSame('Bracia Karamazow', $book->__toString());
    }
}

