<?php

namespace App\BookAction\Repository;

use App\BookAction\Persistence\BookChangeEvent;

class BookChangeEventDTO
{
    private $name;
    private $num;
    private $bookTitle;
    private $bookAuthor;
    private $receiverName;
    private $comment;
    private $date;

    public function __construct(
        string $name,
        string $num,
        string $bookTitle,
        ?string $bookAuthor,
        ?string $receiverName,
        ?string $comment,
        string $date
    ) {
        $this->name = $name;
        $this->num = $num;
        $this->bookTitle = $bookTitle;
        $this->bookAuthor = $bookAuthor;
        $this->receiverName = $receiverName;
        $this->comment = $comment;
        $this->date = $date;
    }

    public function getName(): string
    {
        return BookChangeEvent::NAMES_LABELS[$this->name];
    }

    public function getNum(): string
    {
        return $this->num;
    }

    public function getBookTitle(): string
    {
        return $this->bookTitle;
    }

    public function getBookAuthor(): string
    {
        return $this->bookAuthor ?? '';
    }

    public function getReceiverName(): string
    {
        return $this->receiverName ?? '';
    }

    public function getComment(): string
    {
        return $this->comment ?? '';
    }

    public function getDate(): string
    {
        return $this->date;
    }
}


