<?php

namespace App\BookAction\Domain\Book;

use App\BookAction\Domain\BookChangeEvent;
use App\Catalog\UseCase\BookDTO;
use App\Receiver\UseCase\ReceiverDTO;
use DateTime;
use DomainException;

class Book
{
    private $copies;
    private $bookDTO;

    public function __construct(array $bookChangeEvents, BookDTO $bookDTO)
    {
        $this->copies = new Copies(0);
        $this->bookDTO = $bookDTO;
        $this->calculateEvents($bookChangeEvents);
    }

    public function copies(): Copies
    {
        return $this->copies;
    }

    public function receive(Copies $receivedCopies): BookChangeEvent
    {
        if ($receivedCopies->equalsZero()) {
            throw new DomainException('Only more than zero copies of book can be received');
        }
        $this->copies = $this->copies->add($receivedCopies);
        return new BookChangeEvent(
            BookChangeEvent::RECEIVE,
            $receivedCopies->toInt(),
            new DateTime(),
            $this->bookDTO->id(),
            $this->bookDTO->title(),
            $this->bookDTO->author()
        );
    }

    public function release(Copies $releasedCopies, ReceiverDTO $receiverDTO, string $comment): BookChangeEvent
    {
        if ($releasedCopies->equalsZero()) {
            throw new DomainException('Only more than zero copies of book can be release');
        }
        $this->copies = $this->copies->subtract($releasedCopies);
        $event = new BookChangeEvent(
            BookChangeEvent::RELEASE,
            $releasedCopies->toInt(),
            new DateTime(),
            $this->bookDTO->id(),
            $this->bookDTO->title(),
            $this->bookDTO->author(),
            $receiverDTO->id(),
            $receiverDTO->name()
        );
        if ($comment !== '') {
            $event->setComment($comment);
        }
        return $event;
    }

    public function sell(Copies $soldCopies, string $comment): BookChangeEvent
    {
        if ($soldCopies->equalsZero()) {
            throw new DomainException('Only more than zero copies of book can be sold');
        }
        $this->copies = $this->copies->subtract($soldCopies);
        $event = new BookChangeEvent(
            BookChangeEvent::SELL,
            $soldCopies->toInt(),
            new DateTime(),
            $this->bookDTO->id(),
            $this->bookDTO->title(),
            $this->bookDTO->author()
        );
        if ($comment !== '') {
            $event->setComment($comment);
        }
        return $event;
    }

    /**
     * @param BookChangeEvent[] $events
     */
    private function calculateEvents(array $events): void
    {
        foreach ($events as $event) {
            $this->copies = $this->copies->add($event->copies());
        }
    }
}
