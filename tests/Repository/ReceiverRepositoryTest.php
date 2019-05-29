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
}

