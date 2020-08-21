<?php

namespace App\BookAction\Domain;

interface BookChangeEventWriterInterface
{
    public function saveOne(BookChangeEvent $bookChangeEvent): void;

    /**
     * @param BookChangeEvent[] $bookChangeEvents
     */
    public function saveMany(array $bookChangeEvents): void;
}