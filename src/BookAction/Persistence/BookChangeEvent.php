<?php

namespace App\BookAction\Persistence;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use App\BookAction\Exception\BookChangeEventException;

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

    private const NAMES_LABELS = [
        self::RECEIVE => 'przyjęto',
        self::RELEASE => 'wydano',
        self::SELL => 'sprzedano',
    ];

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\Column(type="integer")
     */
    private $id;
    /**
     * @ORM\GeneratedValue(strategy="UUID")
     * @ORM\Column(type="guid")
     */
    private $uuid;
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
     * @ORM\Column(type="integer")
     */
    private $bookId;
    /**
     * @ORM\Column(type="guid")
     */
    private $bookUuid;
    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $bookTitle;
    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $receiverId;
    /**
     * @ORM\Column(type="guid")
     */
    private $receiverUuid;
    /**
     * @ORM\Column(type="string", length=510, nullable=true)
     */
    private $receiverName;

    /**
     * BookChangeEvent constructor.
     * @param string $name
     * @param int $num
     * @param DateTime $date
     * @param int $bookId
     * @param string $bookTitle
     * @param int|null $receiverId
     * @param string|null $receiverName
     * @throws BookChangeEventException
     */
    public function __construct(
        string $name,
        int $num,
        DateTime $date,
        int $bookId,
        string $bookTitle,
        int $receiverId = null,
        string $receiverName = null
    ) {
        $this->validateName($name);
        $this->validateNum($num);
        $this->name = $name;
        $this->num = $num;
        $this->date = $date;
        $this->bookId = $bookId;
        $this->bookTitle = $bookTitle;
        $this->receiverId = $receiverId;
        $this->receiverName = $receiverName;
    }

    public function getBookId()
    {
        return $this->bookId;
    }

    public function setBookUuid($bookUuid): void
    {
        $this->bookUuid = $bookUuid;
    }

    public function getBookTitle(): string
    {
        return $this->bookTitle;
    }

    public function setBookTitle(string $bookTitle): void
    {
        $this->bookTitle = $bookTitle;
    }

    public function getReceiverId()
    {
        return $this->receiverId;
    }

    public function setReceiverUuid($receiverUuid): void
    {
        $this->receiverUuid = $receiverUuid;
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
        $text = $this->formatDate() . ' pobrał(a) ' . $this->num . ' egz.: ' . $this->bookTitle;
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
