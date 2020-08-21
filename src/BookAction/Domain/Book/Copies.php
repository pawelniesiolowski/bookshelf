<?php

namespace App\BookAction\Domain\Book;

class Copies
{
    private $copies;

    public function __construct(int $copies)
    {
        if ($copies < 0) {
            throw new \InvalidArgumentException('There can\'t be less copies than zero');
        }
        $this->copies = $copies;
    }

    public function add(Copies $copies): Copies
    {
        return new Copies($this->copies + $copies->toInt());
    }

    public function subtract(Copies $copies): Copies
    {
        return new Copies($this->copies - $copies->toInt());
    }

    public function toInt(): int
    {
        return $this->copies;
    }

    public function equalsZero(): bool
    {
        return $this->copies === 0;
    }
}
