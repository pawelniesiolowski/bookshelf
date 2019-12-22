<?php

namespace App\Receiver\Persistence;

use App\BookAction\Persistence\BookChangeEvent;
use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use JsonSerializable;

/**
 * @ORM\Entity(repositoryClass="App\Receiver\Repository\ReceiverRepository")
 */
class Receiver implements JsonSerializable
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
     * @ORM\OneToMany(targetEntity="App\BookAction\Persistence\BookChangeEvent", mappedBy="receiver", orphanRemoval=true)
     * @ORM\OrderBy({"id" = "DESC"})
     */
    private $events;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $deletedAt;

    private $errors = [];

    public function __construct(string $name, string $surname)
    {
        $this->name = $name;
        $this->surname = $surname;
        $this->events = new ArrayCollection();
    }

    public function editFromJsonData(string $data): void
    {
        $data = json_decode($data, true);
        $this->name = $data['name'] ?? '';
        $this->surname = $data['surname'] ?? '';
    }

    public function addEvent(BookChangeEvent $bookChangeEvent): void
    {
        if (!$this->events->contains($bookChangeEvent)) {
            $this->events[] = $bookChangeEvent;
        }
    }

    public function delete(): void
    {
        $this->deletedAt = new DateTime();
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
            'id' => $this->id,
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
        if (!is_string($this->name) || $this->name === '') {
            $this->addError('name', 'Imię użytkownika jest wymagane');
        }
    }

    private function validateSurname(): void
    {
        if (!is_string($this->name) || $this->surname === '') {
            $this->addError('surname', 'Nazwisko użytkownika jest wymagane');
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
        /** @var BookChangeEvent[] $events */
        $events = $this->events->toArray();
        return array_map(function ($event) {
            /** @var BookChangeEvent $event */
            return $event->textFromReceiverPerspective();
        }, $events);
    }
}
