<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use App\Provider\BookChangeEventProvider;

class EventController extends AbstractController
{
    private $entityManager;
    private $bookChangeEventProvider;

    public function __construct(
        EntityManagerInterface $entityManager,
        BookChangeEventProvider $bookChangeEventProvider
    ) {
        $this->entityManager = $entityManager;
        $this->bookChangeEventProvider = $bookChangeEventProvider;
    }

    public function index()
    {
        $events = $this->bookChangeEventProvider->eventsOrderedByDateDesc();
        return $this->json(['events' => $events]);
    }
}

