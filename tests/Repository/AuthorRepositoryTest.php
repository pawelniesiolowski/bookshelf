<?php

namespace App\Tests\Repository;

use App\Tests\FunctionalTestCase;
use App\Entity\Author;
use App\Entity\Book;
use App\Repository\AuthorRepository;
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
            'Bracia Karamazow',
            '0123456789',
            29.99
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
            'Opowieści o pilocie Pirxie',
            '0123456789',
            19.99
        );
        $solaris = new Book(
            'Solaris',
            '1234567890',
            39.99
        );
        $voice = new Book(
            'Głos Pana',
            '9876543210',
            50.00
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

    public function testItShouldGetOneAuthorByNameAndEmail()
    {
        $author = new Author('Fiodor', 'Dostojewski');
        $this->entityManager->persist($author);
        $this->entityManager->flush();
        $this->assertInstanceOf(
            Author::class, 
            $this->authorRepository->findOneByNameAndSurname('Fiodor', 'Dostojewski')
        );
    }

    public function testItShouldReturnNullWhenThereIsNoAuthorByNameAndEmail()
    {
        $this->assertSame(
            null,
            $this->authorRepository->findOneByNameAndSurname('Fiodor', 'Dostojewski')
        );
    }

    public function testItShouldThrowExceptionWhenThereAreTwoAuthorsByNameAndEmail()
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

