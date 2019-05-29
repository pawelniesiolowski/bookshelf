<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ReceiverRepository")
 */
class Receiver implements \JsonSerializable
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
     * @ORM\OneToMany(targetEntity="BookChangeEvent", mappedBy="receiver", orphanRemoval=true)
     * @ORM\OrderBy({"id" = "DESC"})
     */
    private $events;

    private $errors = [];

    public function __construct(string $name, string $surname)
    {
        $this->name = $name;
        $this->surname = $surname;
        $this->events = new ArrayCollection();
    }

    public function addEvent(BookChangeEvent $bookChangeEvent): void
    {
        if (!$this->events->contains($bookChangeEvent)) {
            $this->events[] = $bookChangeEvent;
        }
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

    public function jsonSerialize(): array
    {
        return [
            'name' => $this->__toString(),
            'events' => $this->createJsonSerializableEvents(),
        ];
    }

    public function __toString(): string
    {
        return $this->surname . ' ' . $this->name;
    }

    private function validateName(): void
    {
        if ($this->name === '') {
            $this->addError('name', 'Imię osoby, która może pobrać książki jest wymagane');
        }
    }

    private function validateSurname(): void
    {
        if ($this->surname === '') {
            $this->addError('surname', 'Nazwisko osoby, która może pobrać książki jest wymagane');
        }
    }

    private function addError(string $key, string $desc): void
    {
        if (!array_key_exists($key, $this->errors)) {
            $this->errors[$key] = $desc;
        }
    }

    private function createJsonSerializableEvents(): array
    {
        $events = $this->events->toArray();
        return array_map(function ($event) {
            return $event->textFromReceiverPerspective();
        }, $events);
    }
}

