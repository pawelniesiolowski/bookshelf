<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity(repositoryClass="App\Repository\AuthorRepository")
 */
class Author implements \JsonSerializable
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;
    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;
    /**
     * @ORM\Column(type="string", length=255)
     */
    private $surname;
    /**
     * @ORM\ManyToMany(targetEntity="Book", mappedBy="authors", cascade={"persist", "remove"})
     */
    private $books;

    public function __construct(string $name, string $surname)
    {
        $this->name = $name;
        $this->surname = $surname;
        $this->books = new ArrayCollection();
    }

    public function addBook(Book $book): void
    {
        if (!$this->books->contains($book)) {
            $this->books[] = $book;
            $book->addAuthor($this);
        }
    }

    public function jsonSerialize()
    {
        return [
            'id' => $this->id,
            'name' => $this->surname . ' ' . $this->name,
            'books' => $this->getJsonSerializedBasicSortedBooks(),
        ];
    }

    public function __toString(): string
    {
        return $this->surname . ' ' . $this->name;
    }
    
    private function getJsonSerializedBasicSortedBooks(): array
    {
        $books = $this->books->toArray();

        usort($books, 'strnatcasecmp');

        $books = array_map(function($book) {
            return $book->jsonSerializeBasic();
        }, $books);

        return $books;
    }
}

