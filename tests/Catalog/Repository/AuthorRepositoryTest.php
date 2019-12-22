<?php

namespace App\Tests\Catalog\Repository;

use App\Tests\FunctionalTestCase;
use App\Catalog\Persistence\Author;
use App\Catalog\Persistence\Book;
use App\Catalog\Repository\AuthorRepository;
use Doctrine\ORM\NonUniqueResultException;

class AuthorRepositoryTest extends FunctionalTestCase
{
    private $authorRepository;

    public function setUp()
    {
        parent::setUp();
        $this->authorRepository = new AuthorRepository($this->registry);
    }

    public function testItShouldGetAuthorFromDatabase()
    {
        $author = new Author('Fiodor', 'Dostojewski');
        $book = new Book(
            'Bracia Karamazow'
        );
        $author->addBook($book);
        $this->entityManager->persist($author);
        $this->entityManager->flush();

        $this->assertSame($author, $this->authorRepository->find(1));
    }

    public function testItShouldGetAllAuthorsOrderBySurname()
    {
        $lem = new Author('Stanisław', 'Lem');
        $pirx = new Book(
            'Opowieści o pilocie Pirxie'
        );
        $solaris = new Book(
            'Solaris'
        );
        $voice = new Book(
            'Głos Pana'
        );
        $lem->addBook($pirx);
        $lem->addBook($solaris);
        $lem->addBook($voice);

        $milosz = new Author('Czesław', 'Miłosz');
        $dostojewski = new Author('Fiodor', 'Dostojewski');

        $this->entityManager->persist($lem);
        $this->entityManager->persist($milosz);
        $this->entityManager->persist($dostojewski);
        $this->entityManager->flush();

        $authorsFromRepository = $this->authorRepository->getAllOrderBySurname();
        $this->assertSame('Dostojewski Fiodor', $authorsFromRepository[0]->__toString());
        $this->assertSame('Lem Stanisław', $authorsFromRepository[1]->__toString());
        $this->assertSame('Miłosz Czesław', $authorsFromRepository[2]->__toString());
    }

    public function testItShouldGetOneAuthorByNameAndSurname()
    {
        $author = new Author('Fiodor', 'Dostojewski');
        $this->entityManager->persist($author);
        $this->entityManager->flush();
        $this->assertInstanceOf(
            Author::class, 
            $this->authorRepository->findOneByNameAndSurname('Fiodor', 'Dostojewski')
        );
    }

    public function testItShouldReturnNullWhenThereIsNoAuthorByNameAndSurname()
    {
        $this->assertSame(
            null,
            $this->authorRepository->findOneByNameAndSurname('Fiodor', 'Dostojewski')
        );
    }

    public function testItShouldThrowExceptionWhenThereAreTwoAuthorsByNameAndSurname()
    {
        $firstAuthor = new Author('Fiodor', 'Dostojewski');
        $secAuthor = new Author('Fiodor', 'Dostojewski');
        $this->entityManager->persist($firstAuthor);
        $this->entityManager->persist($secAuthor);
        $this->entityManager->flush();
        $this->expectException(NonUniqueResultException::class);
        $this->authorRepository->findOneByNameAndSurname('Fiodor', 'Dostojewski');
    }
}

