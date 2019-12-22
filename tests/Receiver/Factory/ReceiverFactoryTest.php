<?php

namespace App\Tests\Receiver\Factory;

use PHPUnit\Framework\TestCase;
use App\Receiver\Factory\ReceiverFactory;
use App\Receiver\Persistence\Receiver;

class ReceiverFactoryTest extends TestCase
{
    public function testItShouldCreateReceiverFromJson()
    {
        $receiverFactory = new ReceiverFactory();
        $data = [
            'name' => 'Justyna',
            'surname' => 'Mazur',
        ];
        $receiver = $receiverFactory->fromJson(json_encode($data));
        $this->assertInstanceOf(Receiver::class, $receiver);
        $this->assertSame('Mazur Justyna', $receiver->__toString());
    }
}

