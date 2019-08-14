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

    private $errors = [];

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

    public function validate(): bool
    {
        $this->validateName();
        $this->validateSurname();
        return count($this->errors) === 0;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }

    public function __toString(): string
    {
        return $this->surname . ' ' . $this->name;
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'surname' => $this->surname,
        ];
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

    private function validateName(): void
    {
        if ($this->name === '') {
            $this->addError('name', 'Podaj imię autora');
        }
    }

    private function validateSurname(): void
    {
        if ($this->surname === '') {
            $this->addError('surname', 'Podaj nazwisko autora');
        }
    }

    private function addError(string $key, string $desc): void
    {
        if (!array_key_exists($key, $this->errors)) {
            $this->errors[$key] = $desc;
        }
    }
}

