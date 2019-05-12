<?php

namespace App\Tests\Entity;

use PHPUnit\Framework\TestCase;
use App\Entity\Author;
use App\Entity\Book;

class AuthorTest extends TestCase
{
    public function testJsonSerializeAuthorData()
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

        $jsonSerializedData = [
            'id' => null,
            'name' => 'Lem Stanisław',
            'books' => [
                [
                    'id' => null,
                    'title' => 'Głos Pana',
                    'copies' => 10,
                ],
                [
                    'id' => null,
                    'title' => 'Opowieści o pilocie Pirxie',
                    'copies' => 3,
                ],
                [
                    'id' => null,
                    'title' => 'Solaris',
                    'copies' => 1,
                ],
            ],
        ];
        $this->assertSame($jsonSerializedData, $lem->jsonSerialize());
    }
}

