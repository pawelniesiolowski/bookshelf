<?php

namespace App\Tests\Receiver\Repository;

use App\Tests\FunctionalTestCase;
use App\Receiver\Model\Receiver;

class ReceiverRepositoryTest extends FunctionalTestCase
{
    private $receiverRepository;

    public function setUp(): void
    {
        parent::setUp();
        $this->receiverRepository = $this->entityManager->getRepository(Receiver::class);
    }

    public function testItShouldFindOneById()
    {
        $receiver = new Receiver('Justyna', 'Mazur');
        $this->entityManager->persist($receiver);
        $this->entityManager->flush();
        $this->assertEquals($receiver, $this->receiverRepository->findOneById($receiver->getId()));
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

        $this->assertEquals($expectedData, $this->receiverRepository->findAllNonDeletedOrderedAlphabetically());
    }
}

