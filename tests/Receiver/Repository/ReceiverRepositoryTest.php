<?php

namespace App\Tests\Receiver\Repository;

use App\Tests\FunctionalTestCase;
use App\Receiver\Repository\ReceiverRepository;
use App\Receiver\Persistence\Receiver;

class ReceiverRepositoryTest extends FunctionalTestCase
{
    private $receiverRepository;

    public function setUp(): void
    {
        parent::setUp();
        $this->receiverRepository = new ReceiverRepository($this->registry);
    }

    public function testItShouldFindOneById()
    {
        $receiver = new Receiver('Justyna', 'Mazur');
        $this->entityManager->persist($receiver);
        $this->entityManager->flush();
        $this->assertSame($receiver, $this->receiverRepository->findOneById(1));
    }

    public function testItShouldFindAllNonDeletedOrderAlfabethically()
    {
        $firstReceiver = new Receiver('Paweł', 'Niesiołowski');
        $secondReceiver = new Receiver('Justyna', 'Mazur');
        $thirdReceiver = new Receiver('Alojzy', 'Niesiołowski');
        $fourthReceiver = new Receiver('Gal', 'Anonim');
        $fourthReceiver->delete();
        $this->entityManager->persist($firstReceiver);
        $this->entityManager->persist($secondReceiver);
        $this->entityManager->persist($thirdReceiver);
        $this->entityManager->persist($fourthReceiver);
        $this->entityManager->flush();

        $expectedData = [
            $secondReceiver,
            $thirdReceiver,
            $firstReceiver,
        ];

        $this->assertSame($expectedData, $this->receiverRepository->findAllNonDeletedOrderAlfabethically());
    }
}

