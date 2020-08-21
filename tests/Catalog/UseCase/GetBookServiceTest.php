<?php

namespace App\Tests\Catalog\UseCase;

use App\Catalog\Model\Book;
use App\Catalog\UseCase\BookDTO;
use App\Tests\FunctionalTestCase;
use DomainException;

class GetBookServiceTest extends FunctionalTestCase
{
    public function testItThrowsExceptionIfCreatesBookDTOWithoutIdBeingSet(): void
    {
        self::expectException(DomainException::class);
        $book = new Book('Bracia Karamazow');
        $book->toDTO();
    }

    public function testItCreatesBookDTOWithIdAndTitle()
    {
        $book = new Book('Bracia Karamazow');
        $this->entityManager->persist($book);
        $this->entityManager->flush();
        self::assertInstanceOf(BookDTO::class, $book->toDTO());
    }

    public function testItCreatesBookDTOWithFirstAuthor()
    {
        $book = new Book('Bracia Karamazow');
        $author1 = ['name' => 'Pierwszy', 'surname' => 'Autor'];
        $author2 = ['name' => 'Drugi', 'surname' => 'Autor'];
        $book->addAuthor($author1);
        $book->addAuthor($author2);
        $this->entityManager->persist($book);
        $this->entityManager->flush();

        $bookDTO = $book->toDTO();

        self::assertSame('Pierwszy Autor', $bookDTO->author());
    }

    public function testBookDTOGetsEmptyStringIfAuthorDoesNotExist()
    {
        $book = new Book('Bracia Karamazow');
        $this->entityManager->persist($book);
        $this->entityManager->flush();
        $bookDTO = $book->toDTO();

        self::assertSame('', $bookDTO->author());
    }
}