<?php

namespace App\BookAction;

use App\BookAction\Persistence\BookChangeEvent;
use App\BookAction\Repository\BookChangeEventRepository;
use Doctrine\ORM\EntityManagerInterface;

class ActualizeBookDataService
{
    private $repository;
    private $entityManager;

    public function __construct(BookChangeEventRepository $repository, EntityManagerInterface $entityManager)
    {
        $this->repository = $repository;
        $this->entityManager = $entityManager;
    }

    public function actualizeDataForBook(string $id, string $title, ?string $author): bool
    {
        $events = $this->repository->findAllByBookId($id);
        foreach ($events as $event) {
            /** @var BookChangeEvent $event */
            $event->setBookTitle($title);
            $event->setBookAuthor($author);
            $this->entityManager->persist($event);
        }
        $this->entityManager->flush();
        return true;
    }
}
