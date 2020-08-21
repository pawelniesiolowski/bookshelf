<?php

namespace App\Tests\Receiver\Controller;

use App\Tests\FunctionalTestCase;
use App\Receiver\Model\Receiver;

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

        $receiverRepository = $this->entityManager->getRepository(Receiver::class);
        $receiver = $receiverRepository->findAll();
        $this->assertSame('Mazur Justyna', $receiver[0]->__toString());
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
                    'id' => $secondReceiver->getId(),
                    'name' => 'Mazur Justyna',
                ],
                [
                    'id' => $firstReceiver->getId(),
                    'name' => 'Niesiołowski Paweł',
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
        $client->xmlHttpRequest('GET', '/receiver/' . $receiver->getId());
        $response = $client->getResponse();

        $this->assertSame(200, $response->getStatusCode());
        
        $expectedData = [
            'receiver' => [
                'id' => $receiver->getId(),
                'name' => 'Niesiołowski Paweł',
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
        $client->xmlHttpRequest('DELETE', '/receiver/' . $receiver->getId());
        $response = $client->getResponse();
        $this->assertSame(200, $response->getStatusCode());
    }

    public function testItShouldEditReceiver()
    {
        $receiver = new Receiver('Justyna', 'Mazur');
        $this->entityManager->persist($receiver);
        $this->entityManager->flush();

        $data = [
            'name' => 'Justynka',
            'surname' => 'Mazur',
        ];
        $client = static::createClient();
        $client->xmlHttpRequest('PUT', '/receiver/' . $receiver->getId(), [], [], [], json_encode($data));
        $response = $client->getResponse();

        $this->assertSame(204, $response->getStatusCode());

        $receiverRepository = $this->entityManager->getRepository(Receiver::class);
        $receiver = $receiverRepository->find($receiver->getId());
        $this->assertSame('Mazur Justynka', $receiver->__toString());
    }
}
