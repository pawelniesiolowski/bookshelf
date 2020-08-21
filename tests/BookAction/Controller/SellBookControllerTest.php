<?php

namespace App\Tests\BookAction\Controller;

use App\BookAction\Domain\BookChangeEvent;
use App\Tests\FunctionalTestCase;
use App\Catalog\Model\Book;
use Ramsey\Uuid\Uuid;

class SellBookControllerTest extends FunctionalTestCase
{
    private $bookRepository;

    public function setUp(): void
    {
        parent::setUp();
        $this->bookRepository = $this->entityManager->getRepository(Book::class);
    }

    public function testItShouldSellBook()
    {
        $bookId = $this->addBookWithFiveCopiesToDatabase();

        $data = [
            'copies' => 2,
            'comment' => '',
        ];

        $client = static::createClient();
        $client->xmlHttpRequest('POST', '/sell/' . $bookId, [], [], [], json_encode($data));

        $this->assertSame(204, $client->getResponse()->getStatusCode());

        $serializedBook = $this->bookRepository->find($bookId)->jsonSerializeBasic();
        $this->assertSame(3, $serializedBook['copies']);
    }

    public function testItShouldReturnProperErrorWhenSellsBookWithEmptyString()
    {
        $bookId = $this->addBookWithFiveCopiesToDatabase();

        $data = [
            'copies' => '',
            'comment' => '',
        ];

        $client = static::createClient();
        $client->xmlHttpRequest('POST', '/sell/' . $bookId, [], [], [], json_encode($data));

        $this->assertSame(422, $client->getResponse()->getStatusCode());
    }
    public function testItShouldReturnProperErrorsWhenThereIsLessCopiesThenZero(): void
    {
        $bookId = $this->addBookWithFiveCopiesToDatabase();

        $data = [
            'copies' => -4,
            'comment' => 'Test',
        ];

        $client = static::createClient();

        $client->xmlHttpRequest('POST', '/sell/' . $bookId, [], [], [], json_encode($data));
        $response = $client->getResponse();
        $content = json_decode($response->getContent(), true);

        $this->assertSame(422, $response->getStatusCode());
        $this->assertArrayHasKey('errors', $content);
        $this->assertArrayHasKey('copies', $content['errors']);
    }

    public function testItShouldReturnProperErrorWhenReleaseMoreCopiesThanHas(): void
    {
        $bookId = $this->addBookWithFiveCopiesToDatabase();

        $data = [
            'copies' => 6,
            'comment' => 'Test',
        ];

        $client = static::createClient();

        $client->xmlHttpRequest('POST', '/sell/' . $bookId, [], [], [], json_encode($data));
        $response = $client->getResponse();
        $content = json_decode($response->getContent(), true);

        $this->assertSame(422, $response->getStatusCode());
        $this->assertArrayHasKey('errors', $content);
        $this->assertArrayHasKey('copies', $content['errors']);
    }

    public function testItShouldReturnProperErrorsWhenBookWithGivenIdDoesNotExist(): void
    {
        $randomBookId = Uuid::uuid1()->toString();

        $data = [
            'copies' => 4,
            'comment' => '',
        ];

        $client = static::createClient();

        $client->xmlHttpRequest('POST', '/sell/' . $randomBookId, [], [], [], json_encode($data));
        $response = $client->getResponse();
        $content = json_decode($response->getContent(), true);

        $this->assertSame(422, $response->getStatusCode());
        $this->assertArrayHasKey('errors', $content);
        $this->assertArrayHasKey('book', $content['errors']);
    }

    public function testItAddsCommentToEvent()
    {
        $bookId = $this->addBookWithFiveCopiesToDatabase();

        $data = [
            'copies' => 4,
            'comment' => 'Testowy komentarz',
        ];

        $client = static::createClient();

        $client->xmlHttpRequest('POST', '/sell/' . $bookId, [], [], [], json_encode($data));

        $bookChangeEventRepository = $this->entityManager->getRepository(BookChangeEvent::class);
        /**
         * @var BookChangeEvent[] $events
         */
        $events = $bookChangeEventRepository->findAllByBookId($bookId);

        foreach ($events as $event) {
            if ($event->name() === BookChangeEvent::SELL) {
                self::assertSame('Testowy komentarz', $event->getComment());
            }
        }
    }

    private function addBookWithFiveCopiesToDatabase(): string
    {
        $book = new Book('Bracia Karamazow');
        $this->entityManager->persist($book);
        $this->entityManager->flush();

        $client = static::createClient();
        $client->xmlHttpRequest(
            'POST',
            '/receive/' . $book->getId(),
            [],
            [],
            [],
            json_encode(['copies' => 5])
        );
        return $book->getId();
    }
}