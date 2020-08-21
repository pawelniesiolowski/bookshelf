<?php

namespace App\Catalog\UseCase;

class BookDTO
{
    private $id;
    private $title;
    private $author;

    public function __construct(string $id, string $title, string $author)
    {
        $this->id = $id;
        $this->title = $title;
        $this->author = $author;
    }

    public function id(): string
    {
        return $this->id;
    }

    public function title(): string
    {
        return $this->title;
    }

    public function author(): string
    {
        return $this->author;
    }
}