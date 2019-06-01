<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use App\Entity\BookChangeEvent;
use App\Exception\BookException;
use App\Entity\Receiver;

/**
 * @ORM\Entity(repositoryClass="App\Repository\BookRepository")
 */
class Book implements \JsonSerializable
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
    private $title;
    /**
     * @ORM\Column(type="integer")
     */
    private $copies = 0;
    /**
     * @ORM\Column(type="decimal", precision=7, scale=2)
     */
    private $price;
    /**
     * @ORM\Column(type="string", length=13)
     */
    private $ISBN;
    /**
     * @ORM\ManyToMany(targetEntity="Author", inversedBy="books", cascade={"persist", "remove"})
     * @ORM\JoinTable(name="books_authors")
     */
    private $authors;

    /**
     * @ORM\OneToMany(targetEntity="BookChangeEvent", mappedBy="book", orphanRemoval=true, cascade={"persist", "remove"})
     * @ORM\OrderBy({"id" = "DESC"})
     */
    private $events;

    private $errors = [];

    public function __construct(
        string $title,
        ?string $ISBN,
        ?float $price
    ) {
        $this->title = $title;
        $this->ISBN = $ISBN;
        if ($price !== null) {
            $this->price = round(floatval($price), 2);
        }
        $this->authors = new ArrayCollection();
        $this->events = new ArrayCollection();
    }

    public function addAuthor(Author $author)
    {
        if (!$this->authors->contains($author)) {
            $this->authors[] = $author;
            $author->addBook($this);
        }
    }

    public function receive(int $num): void
    {
        if ($num <= 0) {
            throw new BookException('Nie można przyjąć mniej niż jedną książkę');
        }

        $this->copies += $num;
        $event = new BookChangeEvent(
            BookChangeEvent::RECEIVE,
            $num,
            new \DateTime('now'),
            $this
        );
        $this->addEvent($event);
    }
    
    public function release(int $num, Receiver $receiver): void
    {
        $this->substractCopies($num);
        $event = new BookChangeEvent(
            BookChangeEvent::RELEASE,
            $num,
            new \DateTime('now'),
            $this,
            $receiver
        );
        $this->addEvent($event);
    }

    public function sell(int $num): void
    {
        $this->substractCopies($num);
        $event = new BookChangeEvent(
            BookChangeEvent::SELL,
            $num,
            new \DateTime('now'),
            $this
        );
        $this->addEvent($event);
    }

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'ISBN' => $this->ISBN,
            'price' => (float)$this->price,
            'copies' => $this->copies,
            'author' => $this->createSingleJsonSerializableSortedAuthor(),
        ];
    }

    public function jsonSerializeBasic(): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'copies' => $this->copies,
        ];
    }

    public function jsonSerializeExtended(): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'ISBN' => $this->ISBN,
            'price' => (float)$this->price,
            'copies' => $this->copies,
            'authors' => $this->createJsonSerializableSortedAuthors(),
            'events' => $this->createJsonSerializableEvents(),
        ];
    }

    public function updateFromJson(string $data, array $authors): void
    {
        $data = json_decode($data, true);
        $this->title = $data['title'] ?? '';
        $this->ISBN = $data['ISBN'] ?? null;
        $this->price = $data['price'] ?? null;
        $this->authors->clear();
        foreach ($authors as $author) {
            $this->addAuthor($author);
        }
    }

    public function validate(): bool
    {
        $this->validateTitle();
        $this->validateISBN();
        $this->validateAuthors();
        return count($this->errors) === 0;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }

    public function __toString()
    {
        $text = '';
        $authors = $this->authors->toArray();
        if (count($authors) > 0) {
            $text .= $authors[0]->__toString() . ' ';
        }
        $text .= '"' . $this->title . '"';
        return $text;
    }

    private function createSingleJsonSerializableSortedAuthor(): string
    {
        $authors = $this->authors->toArray();
        return $authors[0]->__toString();
    }

    private function createJsonSerializableSortedAuthors(): array
    {
        $authors = $this->authors->toArray();

        usort($authors, 'strnatcasecmp');

        $authors = array_map(function($author) {
            return $author->__toString();
        }, $authors);

        return $authors;
    }

    private function createJsonSerializableEvents(): array
    {
        $events = $this->events->toArray();
        return array_map(function ($event) {
            return $event->__toString();
        }, $events);
    }

    private function substractCopies(int $num): void
    {
        if ($num <= 0) {
            throw new BookException('Nie można wydać ani sprzedać ujemnej liczby egzemplarzy książki');
        }
        if (($this->copies - $num) < 0) {
            throw new BookException('Książka nie może mieć mniej niż zero egzemplarzy');
        }
        $this->copies -= $num;
    }

    private function addEvent(BookChangeEvent $bookChangeEvent): void
    {
        if (!$this->events->contains($bookChangeEvent)) {
            $this->events[] = $bookChangeEvent;
        }
    }

    private function validateTitle(): void
    {
        if ($this->title === '') {
            $this->addError('title', 'Podaj tytuł');
        }
    }

    private function validateISBN(): void
    {
        if ($this->ISBN !== null && !is_numeric($this->ISBN)) {
            $this->addError('ISBN', 'ISBN musi się składać z samych cyfr');
        }
    }

    private function validateAuthors(): void
    {
        foreach ($this->authors as $author) {
            if (!$author->validate()) {
                $this->addError('authors', 'Podaj imię i nazwisko autora');
            }
        }
    }

    private function addError(string $key, string $desc): void
    {
        if (!array_key_exists($key, $this->errors)) {
            $this->errors[$key] = $desc;
        }
    }
}

