<?php

namespace App\Catalog\DatabaseAction;

use App\BookAction\Persistence\BookChangeEvent;
use App\BookAction\Repository\BookChangeEventRepository;
use App\Catalog\Persistence\Book;
use App\Catalog\Repository\BookRepository;
use App\Receiver\Persistence\Receiver;
use App\Receiver\Repository\ReceiverRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ChangeIdToUuidCommand extends Command
{
    protected static $defaultName = 'app:change-id-to-uuid';
    private $entityManager;
    private $bookChangeEventRepository;
    private $bookRepository;
    private $receiverRepository;

    public function __construct(
        EntityManagerInterface $entityManager,
        BookChangeEventRepository $bookChangeEventRepository,
        BookRepository $bookRepository,
        ReceiverRepository $receiverRepository,
        string $name = null
    ) {
        parent::__construct($name);
        $this->entityManager = $entityManager;
        $this->bookChangeEventRepository = $bookChangeEventRepository;
        $this->bookRepository = $bookRepository;
        $this->receiverRepository = $receiverRepository;
    }

    protected function configure()
    {
        $this
            ->setDescription('Changes id to uuid')
            ->setHelp('Single use command to change id to uuid for bookId and receiverId in BookChangeEventCommand')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln([
            'Changing id to uuid',
            '============',
            '',
        ]);
        $bookEvent = $this->bookChangeEventRepository->findAll();
        foreach ($bookEvent as $event) {
            /** @var BookChangeEvent $event */
            $bookId = $event->getBookId();
            /** @var Book $book */
            $book = $this->bookRepository->find($bookId);
            $receiverId = $event->getReceiverId();
            /** @var Receiver $receiver */
            $event->setBookId($book->getId());
            $event->setBookTitle($book->getTitle());
            if (!is_null($receiverId)) {
                $receiver = $this->receiverRepository->find($receiverId);
                $event->setReceiverId($receiver->getId());
                $event->setReceiverName($receiver->__toString());
            }
            $this->entityManager->persist($event);
        }
        $this->entityManager->flush();
        $output->writeln([
            'Success',
        ]);
        return 0;
    }
}
