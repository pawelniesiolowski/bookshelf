<?php

namespace App\Tests\Controller;

use App\Tests\FunctionalTestCase;
use App\Entity\Receiver;
use App\Repository\ReceiverRepository;

class ReceiverControllerTest extends FunctionalTestCase
{
    public function testItShouldCreateNewReceiver()
    {
        $data = [
            'name' => 'Justyna',
            'surname' => 'Mazur',
        ];
        $client = static::createClient();
        $client->xmlHttpRequest('POST', '/receiver', [], [], [], json_encode($data));
        $response = $client->getResponse();

        $this->assertSame(201, $response->getStatusCode());

        $receiverRepository = new ReceiverRepository($this->registry);
        $receiver = $receiverRepository->find(1);
        $this->assertSame('Mazur Justyna', $receiver->__toString());
    }

    public function testItShouldIndexNonDeletedReceiversInAlfabethicalOrder()
    {
        $firstReceiver = new Receiver('Paweł', 'Niesiołowski');
        $secondReceiver = new Receiver('Justyna', 'Mazur');
        $thirdReceiver = new Receiver('Gal', 'Anonim');
        $thirdReceiver->delete();
        $this->entityManager->persist($firstReceiver);
        $this->entityManager->persist($secondReceiver);
        $this->entityManager->persist($thirdReceiver);
        $this->entityManager->flush();

        $client = static::createClient();
        $client->xmlHttpRequest('GET', '/receiver');
        $response = $client->getResponse();

        $this->assertSame(200, $response->getStatusCode());
        
        $expectedData = [
            'receivers' => [
                [
                    'id' => 2,
                    'name' => 'Mazur Justyna',
                    'events' => [],
                ],
                [
                    'id' => 1,
                    'name' => 'Niesiołowski Paweł',
                    'events' => [],
                ],
            ],
        ];

        $this->assertSame($expectedData, json_decode($response->getContent(), true));
    }

    public function testItShuldGetReceiverById()
    {
        $receiver = new Receiver('Paweł', 'Niesiołowski');
        $this->entityManager->persist($receiver);
        $this->entityManager->flush();

        $client = static::createClient();
        $client->xmlHttpRequest('GET', '/receiver/1');
        $response = $client->getResponse();

        $this->assertSame(200, $response->getStatusCode());
        
        $expectedData = [
            'receiver' => [
                'id' => 1,
                'name' => 'Niesiołowski Paweł',
                'events' => [],
            ],
        ];

        $this->assertSame($expectedData, json_decode($response->getContent(), true));
    }

    public function testItShouldDeleteReceiver()
    {
        $receiver = new Receiver('Justyna', 'Mazur');
        $this->entityManager->persist($receiver);
        $this->entityManager->flush();

        $client = static::createClient();
        $client->xmlHttpRequest('DELETE', '/receiver/1');
        $response = $client->getResponse();
        $this->assertSame(200, $response->getStatusCode());
    }
}

