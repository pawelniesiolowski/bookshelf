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
}

