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
    private $price = 0.0;
    /**
     * @ORM\Column(type="string", length=17, nullable=true)
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

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $deletedAt;

    private $errors = [];

    public function __construct(
        string $title
    ) {
        $this->title = $title;
        $this->authors = new ArrayCollection();
        $this->events = new ArrayCollection();
    }

    public function setPrice(float $price): void
    {
        $this->price = round(floatval($price), 2);
    }

    public function setISBN(?string $ISBN): void
    {
        $this->ISBN = $ISBN;
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
    
    public function release(int $num, Receiver $receiver, string $comment = ''): void
    {
        $this->substractCopies($num);
        $event = new BookChangeEvent(
            BookChangeEvent::RELEASE,
            $num,
            new \DateTime('now'),
            $this,
            $receiver
        );
        $event->setComment($comment);
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

    public function delete(): void
    {
        $this->deletedAt = new \DateTime();
    }

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'ISBN' => $this->ISBN ?? '',
            'price' => $this->price,
            'copies' => $this->copies,
            'authors' => $this->createJsonSerializableSortedAuthors(),
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
            'ISBN' => $this->ISBN ?? '',
            'price' => $this->price,
            'copies' => $this->copies,
            'authors' => $this->createJsonSerializableSortedAuthors(),
            'events' => $this->createJsonSerializableEvents(),
        ];
    }

    public function updateFromJson(string $data, array $authors): void
    {
        $data = json_decode($data, true);
        $this->title = $data['title'] ?? '';
        if (empty($data['ISBN'])) {
            $this->setISBN(null);
        } else {
            $this->setISBN($data['ISBN']);
        }
        if (empty($data['price'])) {
            $this->price = 0.0;
        } else {
            $this->price = is_numeric($data['price']) ? (float)$data['price'] : 0.0;
        }
        $this->authors->clear();
        foreach ($authors as $author) {
            $this->addAuthor($author);
        }
    }

    public function validate(): bool
    {
        $this->errors = [];
        $this->validateTitle();
        if ($this->ISBN !== null) {
            $this->validateISBN();
        }
        $this->validateAuthors();
        $this->validatePrice();
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
        $numOfAuthors = count($authors);
        for ($i = 0; $i < $numOfAuthors; $i++) {
            $text .= $authors[$i]->__toString();
            $text .= ($i < $numOfAuthors - 1 ? ', ' : ' ');
        }
        $text .= '"' . $this->title . '"';
        return $text;
    }

    private function createJsonSerializableSortedAuthors(): array
    {
        $authors = $this->authors->toArray();
        $authors = array_map(function($author) {
            return $author->toArray();
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
        } else if (mb_strlen($this->title, 'utf8') > 100) {
            $this->addError('title', 'Tytuł nie może być dłuższy niż 100 znaków!');
        }
    }

    private function validateISBN(): void
    {
        $isbn = $this->ISBN;
        $digits = str_ireplace(['-', 'X'], '', $isbn);
        if (!is_numeric($digits)) {
            $this->addError('ISBN', 'ISBN musi się składać tylko z cyfr, myślników i znaków "X"!');
            return;
        }

        $digitsAndX = str_ireplace('-', '', $isbn);;
        $length = strlen($digitsAndX);
        if ($length !== 10 && $length !== 13) {
            $this->addError('ISBN', 'ISBN powinien mieć 10 lub 13 cyfr (w tym znak X)!');
            return;
        }

        if (strlen($isbn) > 17) {
            $this->addError('ISBN', 'ISBN razem z myślnikami może mieć maksymalnie 17 znaków!');
            return;
        }
    }

    private function validateAuthors(): void
    {
        for ($i = 0; $i < count($this->authors); $i++) {
            if (!$this->authors[$i]->validate()) {
                $this->errors['authors'][$i] = $this->authors[$i]->getErrors();
            }
        }
    }

    private function validatePrice(): void
    {
        if ($this->price > 99999) {
            $this->addError('price', 'Cena książki nie może być wyższa niż 99999.00 zł');
        }
    }

    private function addError(string $key, string $desc): void
    {
        if (!array_key_exists($key, $this->errors)) {
            $this->errors[$key] = $desc;
        }
    }
}

