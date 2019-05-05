<?php

namespace App\Tests\Entity;

use PHPUnit\Framework\TestCase;
use App\Entity\Author;
use App\Entity\Book;

class AuthorTest extends TestCase
{
    public function testAuthorCreation()
    {
        $author = new Author('Fiodor', 'Dostojewski');
        $this->assertSame('Dostojewski Fiodor', $author->__toString());
    }

    public function testGetBooksShouldReturnBooksOrderedByTitle()
    {
        $lem = new Author('Stanisław', 'Lem');
        $pirx = new Book(
            'Opowieści o pilocie Pirxie',
            3,
            '0123456789',
            19.99
        );
        $solaris = new Book(
            'Solaris',
            1,
            '1234567890',
            39.99
        );
        $voice = new Book(
            'Głos Pana',
            10,
            '9876543210',
            50.00
        );
        $lem->addBook($pirx);
        $lem->addBook($solaris);
        $lem->addBook($voice);

        $books = $lem->getBooks();
        
        $this->assertSame('Głos Pana', $books[0]->__toString());
        $this->assertSame('Opowieści o pilocie Pirxie', $books[1]->__toString());
        $this->assertSame('Solaris', $books[2]->__toString());
    }
}

