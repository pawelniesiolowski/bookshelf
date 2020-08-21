<?php

namespace App\BookAction\Infrastructure;

use App\BookAction\Domain\BookChangeEvent;
use App\BookAction\Domain\BookChangeEventWriterInterface;
use Doctrine\ORM\EntityManagerInterface;

class DoctrineBookChangeEventWriter implements BookChangeEventWriterInterface
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function saveOne(BookChangeEvent $bookChangeEvent): void
    {
        $this->entityManager->persist($bookChangeEvent);
        $this->entityManager->flush();
    }

    public function saveMany(array $bookChangeEvents): void
    {
        foreach ($bookChangeEvents as $bookChangeEvent) {
            $this->entityManager->persist($bookChangeEvent);
        }
        $this->entityManager->flush();
    }
}
