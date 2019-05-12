<?php

namespace App\Tests\Controller;

use App\Tests\FunctionalTestCase;
use App\Entity\Author;
use App\Entity\Book;

class BookshelfControllerTest extends FunctionalTestCase
{
    public function testIndex()
    {
        $dostojewski = new Author('Fiodor', 'Dostojewski');
        $crime = new Book(
            'Zbrodnia i kara',
            2,
            '1234567890',
            29.99
        );
        $idiot = new Book(
            'Idiota',
            1,
            '0987654321',
            19.99
        );
        $dostojewski->addBook($crime);
        $dostojewski->addBook($idiot);
        $lem = new Author('Stanisław', 'Lem');
        $robots = new Book(
            'Bajki robotów',
            4,
            '0123456789',
            59.00
        );
        $lem->addBook($robots);
        $this->entityManager->persist($dostojewski);
        $this->entityManager->persist($lem);
        $this->entityManager->flush();

        $client = static::createClient();
        $client->xmlHttpRequest('GET', '/');
        $response = $client->getResponse();

        $this->assertSame(200, $response->getStatusCode());
        $content = $response->getContent();
        $expectedData = [
            'authors' => [
                [
                    'id' => 1,
                    'name'=> 'Dostojewski Fiodor',
                    'books' => [
                        [
                            'id' => 2,
                            'title' => 'Idiota',
                            'copies' => 1,
                        ],
                        [
                            'id' => 1,
                            'title' => 'Zbrodnia i kara',
                            'copies' => 2,
                        ],
                    ],
                ],
                [
                    'id' => 2,
                    'name' => 'Lem Stanisław',
                    'books' => [
                        [
                            'id' => 3,
                            'title' => 'Bajki robotów',
                            'copies' => 4,
                        ],
                    ],
                ]
            ],
        ];
        $this->assertSame($expectedData, json_decode($content, true));
    }
}

