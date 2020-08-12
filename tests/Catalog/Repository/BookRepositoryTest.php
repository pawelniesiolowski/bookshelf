<?php

namespace App\Tests\Catalog\Repository;

use App\Tests\FunctionalTestCase;
use App\Catalog\Persistence\Book;
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

        $this->entityManager->persist($book);
        $this->entityManager->flush();

        $this->assertEquals($book, $this->bookRepository->find($book->getId()));
    }

    public function testItShouldGetAllBooksFromDatabase()
    {
        $book1 = new Book('Bracia Karamazow');
        $book2 = new Book('Idiota');
        $books = [$book1->jsonSerialize(), $book2->jsonSerialize()];

        $this->entityManager->persist($book1);
        $this->entityManager->persist($book2);
        $this->entityManager->flush();

        $booksFromDatabase = $this->bookRepository->findAll();
        $this->assertCount(2, $booksFromDatabase);
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

