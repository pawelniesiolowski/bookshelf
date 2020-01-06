<?php

namespace App\Catalog\DatabaseAction;

use App\BookAction\Persistence\BookChangeEvent;
use App\BookAction\Repository\BookChangeEventRepository;
use App\Catalog\Persistence\Book;
use App\Catalog\Repository\BookRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class AddFirstAuthorToBookChangeEventTableCommand extends Command
{
    protected static $defaultName = 'app:add-author-to-event-table';
    private $entityManager;
    private $bookRepository;
    private $bookChangeEventRepository;

    public function __construct(
        EntityManagerInterface $entityManager,
        BookRepository $bookRepository,
        BookChangeEventRepository $bookChangeEventRepository,
        string $name = null
    ) {
        parent::__construct($name);
        $this->entityManager = $entityManager;
        $this->bookRepository = $bookRepository;
        $this->bookChangeEventRepository = $bookChangeEventRepository;
    }

    protected function configure()
    {
        $this
            ->setDescription('Adds first author to book_change_event table')
            ->setHelp('Single use command that adds first author author from book table to book_change_event table')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln([
            'Adding first author to book_change_event table',
            '============',
            '',
        ]);
        $books = $this->bookRepository->findAll();
        foreach ($books as $book) {
            /** @var Book $book */
            $events = $this->bookChangeEventRepository->findAllByBookId($book->getId());
            foreach ($events as $event) {
                /** @var BookChangeEvent $event */
                $event->setBookAuthor($book->firstAuthorIfExists());
                $this->entityManager->persist($event);
            }
        }
        $this->entityManager->flush();
        $output->writeln([
            'Success',
        ]);
        return 0;
    }
}
