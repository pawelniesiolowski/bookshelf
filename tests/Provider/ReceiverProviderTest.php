<?php

namespace App\Tests\Provider;

use App\Provider\ReceiverProvider;
use App\Repository\ReceiverRepository;
use App\Entity\Receiver;
use PHPUnit\Framework\TestCase;

class ReceiverProviderTest extends TestCase
{
    private $receiverRepository;
    private $receiverProvider;

    public function setUp()
    {
        $this->receiverRepository = $this->createMock(ReceiverRepository::class);
        $this->receiverProvider = new ReceiverProvider($this->receiverRepository);
    }
    
    public function testIiShouldFindAuthorById()
    {
        $receiver = $this->createMock(Receiver::class);
        $this->receiverRepository->method('findOneById')
            ->with($this->equalTo(1))
            ->will($this->returnValue($receiver));
        $this->assertSame($receiver, $this->receiverProvider->findOneById(1));
    }
}

