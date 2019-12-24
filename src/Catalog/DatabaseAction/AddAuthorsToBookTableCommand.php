<?php

namespace App\Catalog\DatabaseAction;

use App\Catalog\Persistence\Book;
use App\Catalog\Repository\BookRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class AddAuthorsToBookTableCommand extends Command
{
    protected static $defaultName = 'app:add-authors-to-book-table';
    private $entityManager;
    private $bookRepository;

    public function __construct(
        EntityManagerInterface $entityManager,
        BookRepository $bookRepository,
        string $name = null
    ) {
        parent::__construct($name);
        $this->entityManager = $entityManager;
        $this->bookRepository = $bookRepository;
    }

    protected function configure()
    {
        $this
            ->setDescription('Adds authors to book table')
            ->setHelp('Single use command that adds authors from author table to book table')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln([
            'Adding authors to book table',
            '============',
            '',
        ]);
        $books = $this->bookRepository->findAll();
        foreach ($books as $book) {
            /** @var Book $book */
            $authors = $book->createJsonSerializableSortedAuthors();
            $book->setAuthorsNames($authors);
            $this->entityManager->persist($book);
        }
        $this->entityManager->flush();
        $output->writeln([
            'Success',
        ]);
        return 0;
    }
}
