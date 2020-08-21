<?php

namespace App\Tests\BookAction\Controller;

use App\BookAction\Domain\BookChangeEvent;
use App\Catalog\Model\Book;
use App\Receiver\Model\Receiver;
use App\Tests\FunctionalTestCase;
use Ramsey\Uuid\Uuid;

class ReleaseBookControllerTest extends FunctionalTestCase
{
    private $bookRepository;

    public function setUp(): void
    {
        parent::setUp();
        $this->bookRepository = $this->entityManager->getRepository(Book::class);
    }

    public function testItShouldReleaseBook(): void
    {
        $bookId = $this->addBookWithFiveCopiesToDatabase();
        $receiverId = $this->addReceiverToDatabase();

        $data = [
            'copies' => 4,
            'receiver' => $receiverId,
            'comment' => 'Test',
        ];

        $client = static::createClient();
        $client->xmlHttpRequest('POST', '/release/' . $bookId, [], [], [], json_encode($data));

        $this->assertSame(204, $client->getResponse()->getStatusCode());

        $serializedBook = $this->bookRepository->find($bookId)->jsonSerializeBasic();
        $this->assertSame(1, $serializedBook['copies']);
    }

    public function testItShouldReturnProperErrorWhenReleaseBookWithEmptyString(): void
    {
        $bookId = $this->addBookWithFiveCopiesToDatabase();
        $receiverId = $this->addReceiverToDatabase();

        $data = [
            'copies' => '',
            'receiver' => $receiverId,
            'comment' => 'Test',
        ];

        $client = static::createClient();
        $client->xmlHttpRequest('POST', '/release/' . $bookId, [], [], [], json_encode($data));
        $response = $client->getResponse();
        $content = json_decode($response->getContent(), true);

        $this->assertSame(422, $response->getStatusCode());
        $this->assertArrayHasKey('errors', $content);
        $this->assertArrayHasKey('copies', $content['errors']);
    }

    public function testItShouldReturnProperErrorsWhenReceiverIdDoesNotExist(): void
    {
        $bookId = $this->addBookWithFiveCopiesToDatabase();

        $data = [
            'copies' => 4,
            'receiver' => Uuid::uuid1()->toString(),
        ];

        $client = static::createClient();

        $client->xmlHttpRequest('POST', '/release/' . $bookId, [], [], [], json_encode($data));
        $response = $client->getResponse();
        $content = json_decode($response->getContent(), true);

        $this->assertSame(422, $response->getStatusCode());
        $this->assertArrayHasKey('errors', $content);
        $this->assertArrayHasKey('receiver', $content['errors']);
    }

    public function testItShouldReturnProperErrorsWhenReceiverIdIsOfInvalidType(): void
    {
        $bookId = $this->addBookWithFiveCopiesToDatabase();

        $data = [
            'copies' => 4,
            'receiver' => '',
        ];

        $client = static::createClient();

        $client->xmlHttpRequest('POST', '/release/' . $bookId, [], [], [], json_encode($data));
        $response = $client->getResponse();
        $content = json_decode($response->getContent(), true);

        $this->assertSame(422, $response->getStatusCode());
        $this->assertArrayHasKey('errors', $content);
        $this->assertArrayHasKey('receiver', $content['errors']);
    }

    public function testItShouldReturnProperErrorsWhenThereIsLessCopiesThenZero(): void
    {
        $bookId = $this->addBookWithFiveCopiesToDatabase();
        $receiverId = $this->addReceiverToDatabase();

        $data = [
            'copies' => -4,
            'receiver' => $receiverId,
            'comment' => 'Test',
        ];

        $client = static::createClient();

        $client->xmlHttpRequest('POST', '/release/' . $bookId, [], [], [], json_encode($data));
        $response = $client->getResponse();
        $content = json_decode($response->getContent(), true);

        $this->assertSame(422, $response->getStatusCode());
        $this->assertArrayHasKey('errors', $content);
        $this->assertArrayHasKey('copies', $content['errors']);
    }

    public function testItShouldReturnProperErrorWhenReleaseMoreCopiesThanHas(): void
    {
        $bookId = $this->addBookWithFiveCopiesToDatabase();
        $receiverId = $this->addReceiverToDatabase();

        $data = [
            'copies' => 6,
            'receiver' => $receiverId,
            'comment' => 'Test',
        ];

        $client = static::createClient();

        $client->xmlHttpRequest('POST', '/release/' . $bookId, [], [], [], json_encode($data));
        $response = $client->getResponse();
        $content = json_decode($response->getContent(), true);

        $this->assertSame(422, $response->getStatusCode());
        $this->assertArrayHasKey('errors', $content);
        $this->assertArrayHasKey('copies', $content['errors']);
    }

    public function testItShouldReturnProperErrorsWhenBookWithGivenIdDoesNotExist(): void
    {
        $receiverId = $this->addReceiverToDatabase();
        $randomBookId = Uuid::uuid1()->toString();

        $data = [
            'copies' => 4,
            'receiver' => $receiverId,
        ];

        $client = static::createClient();

        $client->xmlHttpRequest('POST', '/release/' . $randomBookId, [], [], [], json_encode($data));
        $response = $client->getResponse();
        $content = json_decode($response->getContent(), true);

        $this->assertSame(422, $response->getStatusCode());
        $this->assertArrayHasKey('errors', $content);
        $this->assertArrayHasKey('book', $content['errors']);
    }

    public function testItAddsCommentToEvent()
    {
        $bookId = $this->addBookWithFiveCopiesToDatabase();
        $receiverId = $this->addReceiverToDatabase();

        $data = [
            'copies' => 4,
            'receiver' => $receiverId,
            'comment' => 'Testowy komentarz',
        ];

        $client = static::createClient();

        $client->xmlHttpRequest('POST', '/release/' . $bookId, [], [], [], json_encode($data));

        $bookChangeEventRepository = $this->entityManager->getRepository(BookChangeEvent::class);
        /**
         * @var BookChangeEvent[] $events
         */
        $events = $bookChangeEventRepository->findAllByBookId($bookId);

        foreach ($events as $event) {
            if ($event->name() === BookChangeEvent::RELEASE) {
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

    private function addReceiverToDatabase(): string
    {
        $receiver = new Receiver('Justyna', 'Mazur');
        $this->entityManager->persist($receiver);
        $this->entityManager->flush();
        return $receiver->getId();
    }
}