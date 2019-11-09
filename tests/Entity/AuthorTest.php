<?php

namespace App\Tests\Entity;

use PHPUnit\Framework\TestCase;
use App\Entity\Author;
use App\Entity\Book;

class AuthorTest extends TestCase
{
    public function testItCanBeJsonSerialized()
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

        $jsonSerializedData = [
            'id' => null,
            'name' => 'Lem Stanisław',
            'books' => [
                [
                    'id' => null,
                    'title' => 'Głos Pana',
                    'copies' => 0,
                ],
                [
                    'id' => null,
                    'title' => 'Opowieści o pilocie Pirxie',
                    'copies' => 0,
                ],
                [
                    'id' => null,
                    'title' => 'Solaris',
                    'copies' => 0,
                ],
            ],
        ];
        $this->assertSame($jsonSerializedData, $lem->jsonSerialize());
    }

    public function testItCanBeUsedAsString()
    {
        $author = new Author('Stanisław', 'Lem');
        $this->assertSame('Lem Stanisław', $author->__toString());
    }

    public function testAuthorWithInvalidDataShouldReturnProperErrors()
    {
        $author = new Author('', '');
        $this->assertSame(false, $author->validate());
        $errors = $author->getErrors();
        $this->assertSame($errors['authorName'], 'Podaj imię autora');
        $this->assertSame($errors['authorSurname'], 'Podaj nazwisko autora');
    }
}

