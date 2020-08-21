<?php

namespace App\Tests\BookAction\Controller;

use App\Tests\FunctionalTestCase;
use App\BookAction\Domain\BookChangeEvent;
use App\Catalog\Model\Book;

class ReceiveBookControllerTest extends FunctionalTestCase
{
    private $bookRepository;

    private $bookChangeEventRepository;

    public function setUp(): void
    {
        parent::setUp();
        $this->bookChangeEventRepository = $this->entityManager->getRepository(BookChangeEvent::class);
        $this->bookRepository = $this->entityManager->getRepository(Book::class);
    }

    public function testItAddEventAndChangeBookCopiesAfterReceiveAction()
    {
        $book = new Book('Bracia Karamazow');
        $this->entityManager->persist($book);
        $this->entityManager->flush();

        $data = ['copies' => 4];

        $client = static::createClient();
        $client->xmlHttpRequest('POST', '/receive/' . $book->getId(), [], [], [], json_encode($data));
        $response = $client->getResponse();

        $this->assertSame(204, $response->getStatusCode());

        $bookChangeEvents = $this->bookChangeEventRepository->findAllByBookId($book->getId());
        $this->assertCount(1, $bookChangeEvents);

        $serializedBook = $this->bookRepository->find($book->getId())->jsonSerializeBasic();
        $this->assertSame(4, $serializedBook['copies']);
    }

    public function testItShouldReturnProperErrorWhenReceivesBookWithEmptyString()
    {
        $book = new Book('Bracia Karamazow');
        $this->entityManager->persist($book);
        $this->entityManager->flush();
        $data = ['copies' => ''];

        $client = static::createClient();
        $client->xmlHttpRequest('POST', '/receive/' . $book->getId(), [], [], [], json_encode($data));
        $response = $client->getResponse();

        $this->assertSame(422, $response->getStatusCode());
    }

    public function testItShowProperErrorWhenReceivesZeroCopies()
    {
        $book = new Book('Bracia Karamazow');
        $this->entityManager->persist($book);
        $this->entityManager->flush();

        $data = ['copies' => 0];

        $client = static::createClient();
        $client->xmlHttpRequest('POST', '/receive/' . $book->getId(), [], [], [], json_encode($data));
        $response = $client->getResponse();

        $this->assertSame(422, $response->getStatusCode());
    }

    public function testItShowProperErrorWhenReceivesLessThanZeroCopies()
    {
        $book = new Book('Bracia Karamazow');
        $this->entityManager->persist($book);
        $this->entityManager->flush();

        $data = ['copies' => -1];

        $client = static::createClient();
        $client->xmlHttpRequest('POST', '/receive/' . $book->getId(), [], [], [], json_encode($data));
        $response = $client->getResponse();

        $this->assertSame(422, $response->getStatusCode());
    }
}
