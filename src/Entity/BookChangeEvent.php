<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Exception\BookChangeEventException;
use App\Entity\Receiver;
use App\Entity\Book;

/**
 * @ORM\Entity(repositoryClass="App\Repository\BookChangeEventRepository")
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
     * @ORM\Column(type="datetime")
     */
    private $date;
    /**
     * @ORM\ManyToOne(targetEntity="Book", inversedBy="events")
     * @ORM\JoinColumn(nullable=false)
     */
    private $book;
    /**
     * @ORM\ManyToOne(targetEntity="Receiver", inversedBy="events")
     * @ORM\JoinColumn(nullable=false)
     */
    private $receiver;

    public function __construct(
        string $name,
        int $num,
        \DateTime $date,
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

    public function textFromReceiverPerspective(): string
    {
        return $this->formatDate() . ' pobrał(a) ' . $this->num . ' egz.: ' . $this->book->__toString();
    }

    public function __toString()
    {
        $text = $this->whenAndHowMany();
        if ($this->receiver !== null) {
            $text .= ' Pobrał(a): ' . $this->receiver->__toString();
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

    private function validateName(string $name): void
    {
        if (!in_array($name, self::VALID_NAMES)) {
            throw new BookChangeEventException('Event name is invalid');
        }
    }

    private function validateNum(int $num): void
    {
        if ($num <= 0) {
            throw new BookChangeEventException('Number must be greater then 0');
        }
    }
}

