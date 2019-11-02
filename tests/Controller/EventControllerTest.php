<?php

namespace App\Tests\Controller;

use App\Tests\FunctionalTestCase;
use App\Entity\Book;
use App\Entity\Receiver;

class EventControllerTest extends FunctionalTestCase
{
    public function testItShouldIndexAllEvents()
    {
        $book = new Book('Bracia Karamazow');
        $this->entityManager->persist($book);

        $receiver = new Receiver('Justyna', 'Mazur');
        $this->entityManager->persist($receiver);

        $book->receive(5);
        $book->release(2, $receiver, 'Testowy komentarz');
        $book->sell(2);

        $this->entityManager->flush();

        $client = static::createClient();
        $client->xmlHttpRequest('GET', '/events');
        $response = $client->getResponse();

        $this->assertSame(200, $response->getStatusCode());

        $content = json_decode($response->getContent(), true);
        $this->assertCount(3, $content['events']);
    }
}

