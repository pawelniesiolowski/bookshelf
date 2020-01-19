<?php

namespace App\BookAction\Persistence;

use DateTime as DateTimeAlias;
use Doctrine\ORM\Mapping as ORM;
use App\BookAction\Exception\BookChangeEventException;
use Ramsey\Uuid\UuidInterface;

/**
 * @ORM\Entity(repositoryClass="App\BookAction\Repository\BookChangeEventRepository")
 */
class BookChangeEvent
{
    public const RECEIVE = 'Receive';
    public const RELEASE = 'Release';
    public const SELL = 'Sell';

    private const VALID_NAMES = [
        self::RECEIVE,
        self::RELEASE,
        self::SELL,
    ];

    public const NAMES_LABELS = [
        self::RECEIVE => 'przyjęto',
        self::RELEASE => 'wydano',
        self::SELL => 'sprzedano',
    ];

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
    private $name;
    /**
     * @ORM\Column(type="integer")
     */
    private $num;
    /**
     * @ORM\Column(type="string", length=500, nullable=true)
     */
    private $comment;
    /**
     * @ORM\Column(type="datetime")
     */
    private $date;
    /**
     * @ORM\Column(type="uuid")
     */
    private $bookId;
    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $bookTitle;
    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $bookAuthor;
    /**
     * @ORM\Column(type="uuid", nullable=true)
     */
    private $receiverId;
    /**
     * @ORM\Column(type="string", length=510, nullable=true)
     */
    private $receiverName;

    /**
     * BookChangeEvent constructor.
     * @param string $name
     * @param int $num
     * @param DateTimeAlias $date
     * @param string $bookId
     * @param string $bookTitle
     * @param string $bookAuthor
     * @param string|null $receiverId
     * @param string|null $receiverName
     * @throws BookChangeEventException
     */
    public function __construct(
        string $name,
        int $num,
        DateTimeAlias $date,
        string $bookId,
        string $bookTitle,
        string $bookAuthor = null,
        string $receiverId = null,
        string $receiverName = null
    ) {
        $this->validateName($name);
        $this->validateNum($num);
        $this->name = $name;
        $this->num = $num;
        $this->date = $date;
        $this->bookId = $bookId;
        $this->bookTitle = $bookTitle;
        $this->bookAuthor = $bookAuthor;
        $this->receiverId = $receiverId;
        $this->receiverName = $receiverName;
    }

    public function setBookId($bookId): void
    {
        $this->bookId = $bookId;
    }

    public function getBookTitle(): string
    {
        return $this->bookTitle;
    }

    public function setBookTitle(string $bookTitle): void
    {
        $this->bookTitle = $bookTitle;
    }

    public function setBookAuthor(?string $bookAuthor): void
    {
        $this->bookAuthor = $bookAuthor;
    }

    public function setReceiverId($receiverId): void
    {
        $this->receiverId = $receiverId;
    }

    /**
     * @return string|null
     */
    public function getReceiverName(): ?string
    {
        return $this->receiverName;
    }

    /**
     * @param string|null $receiverName
     */
    public function setReceiverName(?string $receiverName): void
    {
        $this->receiverName = $receiverName;
    }

    public function setComment(string $comment): void
    {
        if ($comment !== '') {
            $this->comment = $comment;
        }
    }

    public function getComment(): string
    {
        return $this->comment ?? '';
    }

    public function textFromReceiverPerspective(): string
    {
        $text = $this->formatDate() . ' pobrał(a) ' . $this->num . ' egz.: "' . $this->bookTitle . '"';
        if ($this->getComment()) {
            $text .= '. Komentarz: ' . $this->getComment();
        }
        return $text;
    }

    public function __toString()
    {
        $text = $this->whenAndHowMany();
        if ($this->receiverName !== null) {
            $text .= ' Pobrał(a): ' . $this->receiverName;
        }

        if ($this->getComment() !== '') {
            $text .= '. Komentarz: ' . $this->getComment();
        }
        return $text;
    }

    private function whenAndHowMany(): string
    {
        return $this->formatDate() .
        ': ' .
        self::NAMES_LABELS[$this->name] .
        ' ' .
        $this->num .
        ' egz.';
    }

    private function formatDate(): string
    {
        return $this->date->format('d-m-Y');
    }

    /**
     * @param string $name
     * @throws BookChangeEventException
     */
    private function validateName(string $name): void
    {
        if (!in_array($name, self::VALID_NAMES)) {
            throw new BookChangeEventException('Event name is invalid');
        }
    }

    /**
     * @param int $num
     * @throws BookChangeEventException
     */
    private function validateNum(int $num): void
    {
        if ($num <= 0) {
            throw new BookChangeEventException('Number must be greater then 0');
        }
    }
}
