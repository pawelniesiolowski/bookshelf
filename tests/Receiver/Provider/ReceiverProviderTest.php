<?php

namespace App\Tests\Receiver\Provider;

use App\Receiver\Provider\ReceiverProvider;
use App\Receiver\Repository\ReceiverRepository;
use App\Receiver\Persistence\Receiver;
use PHPUnit\Framework\TestCase;

class ReceiverProviderTest extends TestCase
{
    private $receiverRepository;
    private $receiverProvider;

    public function setUp(): void
    {
        $this->receiverRepository = $this->createMock(ReceiverRepository::class);
        $this->receiverProvider = new ReceiverProvider($this->receiverRepository);
    }
    
    public function testItShouldFindReceiverById()
    {
        $receiver = $this->createMock(Receiver::class);
        $this->receiverRepository->method('findOneById')
            ->with($this->equalTo(1))
            ->will($this->returnValue($receiver));
        $this->assertSame($receiver, $this->receiverProvider->findOneById(1));
    }

    public function testItShouldFindAllReceivers()
    {
        $receiver = $this->createMock(Receiver::class);
        $this->receiverRepository->method('findAllNonDeletedOrderAlfabethically')
            ->will($this->returnValue([$receiver]));
        $this->assertSame([$receiver], $this->receiverProvider->findAll());
    }
}

