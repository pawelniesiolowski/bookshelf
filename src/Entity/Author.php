<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity(repositoryClass="App\Repository\AuthorRepository")
 */
class Author
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

    public function getBooks(): array
    {
        $books = $this->books->toArray();
        usort($books, 'strnatcasecmp');
        return $books;
    }

    public function __toString(): string
    {
        return $this->surname . ' ' . $this->name;
    }
}

