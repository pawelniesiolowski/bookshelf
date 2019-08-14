<?php

namespace App\Tests\Controller;

use App\Tests\FunctionalTestCase;
use App\Entity\Author;
use App\Entity\Book;
use App\Entity\Receiver;
use App\Repository\BookRepository;

class BookshelfControllerTest extends FunctionalTestCase
{
    private $bookRepository;

    public function setUp()
    {
        parent::setUp();
        $this->bookRepository = new BookRepository($this->registry);
    }

    public function testIndex()
    {
        $dostojewski = new Author('Fiodor', 'Dostojewski');
        $crime = new Book('Zbrodnia i kara');
        $crime->setISBN('1234567890');
        $crime->setPrice(29.99);
        $idiot = new Book('Idiota');
        $idiot->setISBN('0987654321');
        $idiot->setPrice(19.99);
        $crime->addAuthor($dostojewski);
        $idiot->addAuthor($dostojewski);
        $lem = new Author('Stanisław', 'Lem');
        $robots = new Book('Bajki robotów');
        $robots->setISBN('0123456789');
        $robots->setPrice(59.00);
        $robots->addAuthor($lem);
        $this->entityManager->persist($crime);
        $this->entityManager->persist($robots);
        $this->entityManager->persist($idiot);
        $this->entityManager->flush();

        $client = static::createClient();
        $client->xmlHttpRequest('GET', '/books');
        $response = $client->getResponse();

        $this->assertSame(200, $response->getStatusCode());
        $content = $response->getContent();
        $expectedData = [
            'books' => [
                [
                    'id' => 2,
                    'title' => 'Idiota',
                    'ISBN' => '0987654321',
                    'price' => '19.99',
                    'copies' => 0,
                    'author' => [
                        'name' => 'Fiodor',
                        'surname' => 'Dostojewski',
                    ],
                ],
                [
                    'id' => 1,
                    'title' => 'Zbrodnia i kara',
                    'ISBN' => '1234567890',
                    'price' => '29.99',
                    'copies' => 0,
                    'author' => [
                        'name' => 'Fiodor',
                        'surname' => 'Dostojewski',
                    ],
                ],
                [
                    'id' => 3,
                    'title' => 'Bajki robotów',
                    'ISBN' => '0123456789',
                    'price' => '59.00',
                    'copies' => 0,
                    'author' => [
                        'name' => 'Stanisław',
                        'surname' => 'Lem',
                    ],
                ],
            ],
        ];
        $this->assertSame($expectedData, json_decode($content, true));
    }

    public function testItShouldReceiveBook()
    {
        $book = new Book('Bracia Karamazow');
        $this->entityManager->persist($book);
        $this->entityManager->flush();
        $data = ['copies' => 4];

        $client = static::createClient();
        $client->xmlHttpRequest('POST', '/receive/1', [], [], [], json_encode($data));
        $response = $client->getResponse();

        $this->assertSame(204, $response->getStatusCode());

        $serializedBook = $this->bookRepository->find(1)->jsonSerializeBasic();
        $this->assertSame(4, $serializedBook['copies']);
    }
    
    public function testItShouldReleaseBook()
    {
        $book = new Book('Bracia Karamazow');
        $book->receive(5);
        $this->entityManager->persist($book);

        $receiver = new Receiver('Justyna', 'Mazur');
        $this->entityManager->persist($receiver);
        $this->entityManager->flush();

        $data = [
            'copies' => 4,
            'receiverId' => 1,
            'comment' => 'Test',
        ];

        $client = static::createClient();
        $client->xmlHttpRequest('POST', '/release/1', [], [], [], json_encode($data));
        $response = $client->getResponse();

        $this->assertSame(204, $response->getStatusCode());

        $serializedBook = $this->bookRepository->find(1)->jsonSerializeBasic();
        $this->assertSame(1, $serializedBook['copies']);
    }

    public function testItShouldReturnProperErrorsWhenReceiverIdIsInvalid()
    {
        $book = new Book('Bracia Karamazow', '0123456789', 29.99);
        $book->receive(5);
        $this->entityManager->persist($book);
        $this->entityManager->flush();

        $data = [
            'copies' => 4,
            'receiverId' => 1,
        ];

        $client = static::createClient();

        $client->xmlHttpRequest('POST', '/release/1', [], [], [], json_encode($data));
        $response = $client->getResponse();
        $content = json_decode($response->getContent(), true);

        $this->assertSame(422, $response->getStatusCode());
        $this->assertArrayHasKey('errors', $content);
        $this->assertArrayHasKey('receiverId', $content['errors']);
    }
    
    public function testItShouldReturnProperErrorsWhenThereIsLessCopiesThenZero()
    {
        $book = new Book('Bracia Karamazow', '0123456789', 29.99);
        $book->receive(5);
        $this->entityManager->persist($book);
        $this->entityManager->flush();

        $receiver = new Receiver('Justyna', 'Mazur');
        $this->entityManager->persist($receiver);
        $this->entityManager->flush();

        $data = [
            'copies' => -4,
            'receiverId' => 1,
            'comment' => 'Test',
        ];

        $client = static::createClient();

        $client->xmlHttpRequest('POST', '/release/1', [], [], [], json_encode($data));
        $response = $client->getResponse();
        $content = json_decode($response->getContent(), true);

        $this->assertSame(422, $response->getStatusCode());
        $this->assertArrayHasKey('errors', $content);
        $this->assertArrayHasKey('copies', $content['errors']);
    }

    public function testItShouldSellBook()
    {
        $book = new Book('Bracia Karamazow', '0123456789', 29.99);
        $book->receive(5);
        $this->entityManager->persist($book);
        $this->entityManager->flush();

        $data = [
            'copies' => 2,
        ];

        $client = static::createClient();
        $client->xmlHttpRequest('POST', '/sell/1', [], [], [], json_encode($data));
        $response = $client->getResponse();

        $this->assertSame(204, $response->getStatusCode());

        $serializedBook = $this->bookRepository->find(1)->jsonSerializeBasic();
        $this->assertSame(3, $serializedBook['copies']);
    }
}

