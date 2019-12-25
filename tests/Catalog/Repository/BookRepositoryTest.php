<?php

namespace App\Tests\Catalog\Repository;

use App\Tests\FunctionalTestCase;
use App\Catalog\Persistence\Book;
use App\Catalog\Persistence\Author;
use App\Catalog\Repository\BookRepository;

class BookRepositoryTest extends FunctionalTestCase
{
    private $bookRepository;
    public function setUp(): void
    {
        parent::setUp();
        $this->bookRepository = new BookRepository($this->registry);
    }

    public function testItShouldGetBookFromDatabase()
    {
        $book = new Book(
            'BraciaKaramazow'
        );
        $book->addAuthor(new Author('Fiodor', 'Dostojewski'));
        
        $this->entityManager->persist($book);
        $this->entityManager->flush();

        $this->assertSame($book, $this->bookRepository->find(1));
    }

    public function testItShouldGetAllBooksFromDatabase()
    {
        $book1 = new Book(
            'BraciaKaramazow'
        );
        $book1->addAuthor(new Author('Fiodor', 'Dostojewski'));
        $book2 = new Book(
            'Idiota'
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

    public function testItShouldFindAllBooksOrderedByTitle()
    {
        $firstBook = new Book('Solaris');
        $secondBook = new Book('Biblia');
        $thirdBook = new Book('Bracia Karamazow');

        $this->entityManager->persist($firstBook);
        $this->entityManager->persist($secondBook);
        $this->entityManager->persist($thirdBook);
        $this->entityManager->flush();

        $books = $this->bookRepository->findAllOrderedByTitle();
        $this->assertSame('"Biblia"', $books[0]->__toString());
        $this->assertSame('"Bracia Karamazow"', $books[1]->__toString());
        $this->assertSame('"Solaris"', $books[2]->__toString());
    }
}

