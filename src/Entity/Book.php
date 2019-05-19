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

    public function __construct(
        string $title,
        string $ISBN,
        float $price
    ) {
        $this->title = $title;
        $this->ISBN = $ISBN;
        $this->price = $price;
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

    public function jsonSerializeBasic(): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'copies' => $this->copies,
        ];
    }

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'ISBN' => $this->ISBN,
            'price' => $this->price,
            'copies' => $this->copies,
            'authors' => $this->createJsonSerializableSortedAuthors(),
            'events' => $this->createJsonSerializableEvents(),
        ];
    }

    public function __toString()
    {
        $authors = array_map(function($author) {
            return $author->__toString();
        }, $this->authors->toArray());
        return implode(', ', $authors) . ' "' . $this->title . '"';
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
        if ($this->copies - $num < 0) {
            throw new BookException('There can not be less copies then zero');
        }
        $this->copies -= $num;
    }

    private function addEvent(BookChangeEvent $bookChangeEvent): void
    {
        if (!$this->events->contains($bookChangeEvent)) {
            $this->events[] = $bookChangeEvent;
        }
    }
}

