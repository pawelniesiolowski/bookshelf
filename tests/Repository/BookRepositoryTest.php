<?php

namespace App\Tests\Repository;

use App\Tests\FunctionalTestCase;
use App\Entity\Book;
use App\Entity\Author;
use App\Repository\BookRepository;

class BookRepositoryTest extends FunctionalTestCase
{
    private $bookRepository;

    public function setUp()
    {
        parent::setUp();
        $this->bookRepository = new BookRepository($this->registry);
    }

    public function testItShouldGetBookFromDatabase()
    {
        $book = new Book(
            'BraciaKaramazow',
            '0123456789',
            29.99
        );
        $book->addAuthor(new Author('Fiodor', 'Dostojewski'));
        
        $this->entityManager->persist($book);
        $this->entityManager->flush();

        $this->assertSame($book, $this->bookRepository->find(1));
    }

    public function testItShouldGetAllBooksFromDatabase()
    {
        $book1 = new Book(
            'BraciaKaramazow',
            '0123456789',
            29.99
        );
        $book1->addAuthor(new Author('Fiodor', 'Dostojewski'));
        $book2 = new Book(
            'Idiota',
            '9876543210',
            39.99
        );
        $book2->addAuthor(new Author('Fiodor', 'Dostojewski'));
        $books = [];
        $books[] = $book1;
        $books[] = $book2;

        $this->entityManager->persist($book1);
        $this->entityManager->persist($book2);
        $this->entityManager->flush();

        $this->assertSame($books, $this->bookRepository->findAll());
    }
}

