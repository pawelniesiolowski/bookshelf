<?php

namespace App\Catalog\Model;

use App\Catalog\UseCase\BookDTO;
use DateTime;
use Doctrine\ORM\Mapping as ORM;
use DomainException;
use JsonSerializable;
use Ramsey\Uuid\UuidInterface;

/**
 * @ORM\Entity(repositoryClass="App\Catalog\Repository\BookRepository")
 */
class Book implements JsonSerializable
{
    /**
     * @var UuidInterface
     *
     * @ORM\Id
     * @ORM\Column(type="uuid", unique=true)
     * @ORM\GeneratedValue(strategy="CUSTOM")
     * @ORM\CustomIdGenerator(class="Ramsey\Uuid\Doctrine\UuidGenerator")
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
     * @ORM\Column(type="string", length=500, nullable=true)
     */
    private $authors = '[]';

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $deletedAt;

    private $errors = [];

    public function __construct(string $title)
    {
        $this->title = $title;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function setCopies(int $copies): void
    {
        if ($copies < 0) {
            throw new DomainException('There must not be less book copies then zero');
        }
        $this->copies = $copies;
    }

    public function setPrice(float $price): void
    {
        $this->price = round(floatval($price), 2);
    }

    public function setISBN(?string $ISBN): void
    {
        $this->ISBN = $ISBN;
    }

    public function getAuthors(): array
    {
        return json_decode($this->authors, true);
    }

    public function addAuthor(array $author)
    {
        $authors = $this->getAuthors();
        if (!in_array($author, $authors)) {
            $authors[] = $author;
            $this->setAuthors($authors);
        }
    }

    public function toDTO(): BookDTO
    {
        if (empty($this->id)) {
            throw new DomainException('Book id must be set before creating BookDTO');
        }
        $authors = $this->getAuthors();
        $author = array_shift($authors);
        $author = implode(' ', $author ?? []);
        return new BookDTO(
            $this->id,
            $this->title,
            $author
        );
    }

    public function delete(): void
    {
        $this->deletedAt = new DateTime();
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
        $this->setAuthors($authors);
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
        $authors = $this->getAuthors();
        $numOfAuthors = count($authors);
        for ($i = 0; $i < $numOfAuthors; $i++) {
            $text .= $authors[$i]['surname'] . ' ' . $authors[$i]['name'];
            $text .= ($i < $numOfAuthors - 1 ? ', ' : ' ');
        }
        $text .= '"' . $this->title . '"';
        return $text;
    }

    public function createJsonSerializableSortedAuthors(): array
    {
        return $this->getAuthors();
    }

    private function setAuthors(array $authors): void
    {
        $this->authors = json_encode($authors);
    }

    private function validateTitle(): void
    {
        if ($this->title === '') {
            $this->addError('title', 'Podaj tytuł');
        } else if (mb_strlen($this->title, 'utf8') > 100) {
            $this->addError('title', 'Tytuł nie może być dłuższy niż 100 znaków');
        }
    }

    private function validateISBN(): void
    {
        $isbn = $this->ISBN;
        $digits = str_ireplace(['-', 'X'], '', $isbn);
        if (!is_numeric($digits)) {
            $this->addError('ISBN', 'ISBN musi się składać tylko z cyfr, myślników i znaków "X"');
            return;
        }

        $digitsAndX = str_ireplace('-', '', $isbn);
        $length = strlen($digitsAndX);
        if ($length !== 10 && $length !== 13) {
            $this->addError('ISBN', 'ISBN powinien mieć 10 lub 13 cyfr (możliwy jest też znak X)');
            return;
        }

        if (strlen($isbn) > 17) {
            $this->addError('ISBN', 'ISBN razem z myślnikami może mieć maksymalnie 17 znaków');
            return;
        }
    }

    private function validateAuthors(): void
    {
        $authors = $this->getAuthors();
        for ($i = 0; $i < count($authors); $i++) {
            if (empty($authors[$i]['name'])) {
                $this->errors['authors'][$i]['authorName'] = 'Podaj imię autora';
            }
            if (empty($authors[$i]['surname'])) {
                $this->errors['authors'][$i]['authorSurname'] = 'Podaj nazwisko autora';
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
