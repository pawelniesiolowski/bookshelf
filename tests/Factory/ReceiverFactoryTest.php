<?php

namespace App\Tests\Factory;

use PHPUnit\Framework\TestCase;
use App\Factory\ReceiverFactory;
use App\Entity\Receiver;

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

