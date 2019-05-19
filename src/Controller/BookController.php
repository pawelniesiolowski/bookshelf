<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Factory\BookFactory;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;

class BookController extends AbstractController
{
    private $bookFactory;
    private $entityManager;

    public function __construct(
        BookFactory $bookFactory,
        EntityManagerInterface $entityManager
    ) {
        $this->bookFactory = $bookFactory;
        $this->entityManager = $entityManager;
    }

    public function new(Request $request)
    {
        $book = $this->bookFactory->fromJson($request->getContent());
        $this->entityManager->persist($book);
        $this->entityManager->flush();
        
        return $this->json([], 201);
    }
}

