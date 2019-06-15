<?php

namespace App\Tests\Repository;

use App\Tests\FunctionalTestCase;
use App\Repository\ReceiverRepository;
use App\Entity\Receiver;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;

class ReceiverRepositoryTest extends FunctionalTestCase
{
    private $receiverRepository;

    public function setUp()
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

    public function testItShouldFindAllOrderAlfabethically()
    {
        $firstReceiver = new Receiver('Paweł', 'Niesiołowski');
        $secondReceiver = new Receiver('Justyna', 'Mazur');
        $thirdReceiver = new Receiver('Alojzy', 'Niesiołowski');
        $this->entityManager->persist($firstReceiver);
        $this->entityManager->persist($secondReceiver);
        $this->entityManager->persist($thirdReceiver);
        $this->entityManager->flush();

        $expectedData = [
            $secondReceiver,
            $thirdReceiver,
            $firstReceiver,
        ];

        $this->assertSame($expectedData, $this->receiverRepository->findAllOrderAlfabethically());
    }
}

