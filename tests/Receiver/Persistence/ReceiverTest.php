<?php

namespace App\Tests\Receiver\Persistence;

use PHPUnit\Framework\TestCase;
use App\Receiver\Model\Receiver;
use App\BookAction\Domain\BookChangeEvent;

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
        $jsonSerializedReceiver = [
            'id' => null,
            'name' => 'Mazur Justyna',
        ];
        $this->assertSame($jsonSerializedReceiver, $receiver->jsonSerialize());
    }

    public function testItShouldReturnErrorsWhenGetsInvalidData()
    {
        $receiver = new Receiver('', '');
        $this->assertSame(false, $receiver->validate());
        $errors = $receiver->getErrors();
        $this->assertArrayHasKey('name', $errors);
        $this->assertArrayHasKey('surname', $errors);
    }

    public function testItShouldBeEditedFromJsonData()
    {
        $receiver = new Receiver('Justyna', 'Mazur');
        $data = [
            'name' => 'Justynka',
            'surname' => 'Mazur',
        ];
        $jsonData = json_encode($data);
        $receiver->editFromJsonData($jsonData);
        $this->assertSame('Mazur Justynka', $receiver->__toString());
    }
}
