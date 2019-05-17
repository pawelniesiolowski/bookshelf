<?php

namespace App\Tests\Entity;

use PHPUnit\Framework\TestCase;
use App\Entity\Receiver;
use App\Entity\BookChangeEvent;

class ReceiverTest extends TestCase
{
    public function testReceiverCreation()
    {
        $receiver = new Receiver('Justyna', 'Mazur');
        $this->assertSame('Mazur Justyna', $receiver->__toString());
    }

    public function testItCanBeJsonSerialized()
    {
        $firstBookChangeEvent = $this->createMock(BookChangeEvent::class);
        $firstBookChangeEvent->method('textFromReceiverPerspective')
            ->will($this->returnValue('first text from receiver perspective'));

        $secBookChangeEvent = $this->createMock(BookChangeEvent::class);
        $secBookChangeEvent->method('textFromReceiverPerspective')
            ->will($this->returnValue('second text from receiver perspective'));
        
        $receiver = new Receiver('Justyna', 'Mazur');
        $receiver->addEvent($firstBookChangeEvent);
        $receiver->addEvent($secBookChangeEvent);
        $jsonSerializedReceiver = [
            'name' => 'Mazur Justyna',
            'events' => [
                'first text from receiver perspective',
                'second text from receiver perspective',
            ],
        ];
        $this->assertSame($jsonSerializedReceiver, $receiver->jsonSerialize());
    }
}

