<?php

namespace App\BookAction\Persistence;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use App\BookAction\Exception\BookChangeEventException;
use App\Receiver\Persistence\Receiver;
use App\Catalog\Persistence\Book;

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
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
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
     * @ORM\ManyToOne(targetEntity="App\Catalog\Persistence\Book", inversedBy="events")
     * @ORM\JoinColumn(nullable=false)
     */
    private $book;
    /**
     * @ORM\ManyToOne(targetEntity="App\Receiver\Persistence\Receiver", inversedBy="events")
     * @ORM\JoinColumn(nullable=true)
     */
    private $receiver;

    /**
     * BookChangeEvent constructor.
     * @param string $name
     * @param int $num
     * @param DateTime $date
     * @param Book $book
     * @param Receiver|null $receiver
     * @throws BookChangeEventException
     */
    public function __construct(
        string $name,
        int $num,
        DateTime $date,
        Book $book,
        Receiver $receiver = null
    ) {
        $this->validateName($name);
        $this->validateNum($num);
        $this->name = $name;
        $this->num = $num;
        $this->date = $date;
        $this->book = $book;
        $this->receiver = $receiver;
        if ($this->receiver !== null) {
            $this->receiver->addEvent($this);
        }
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
        $text = $this->formatDate() . ' pobrał(a) ' . $this->num . ' egz.: ' . $this->book->__toString();
        if ($this->getComment()) {
            $text .= '. Komentarz: ' . $this->getComment();
        }
        return $text;
    }

    public function __toString()
    {
        $text = $this->whenAndHowMany();
        if ($this->receiver !== null) {
            $text .= ' Pobrał(a): ' . $this->receiver->__toString();
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
