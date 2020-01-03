<?php

namespace App\Receiver\Persistence;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use JsonSerializable;
use Ramsey\Uuid\UuidInterface;

/**
 * @ORM\Entity(repositoryClass="App\Receiver\Repository\ReceiverRepository")
 */
class Receiver implements JsonSerializable
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
    private $name;
    /**
     * @ORM\Column(type="string", length=255)
     */
    private $surname;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $deletedAt;

    private $errors = [];

    public function __construct(string $name, string $surname)
    {
        $this->name = $name;
        $this->surname = $surname;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function editFromJsonData(string $data): void
    {
        $data = json_decode($data, true);
        $this->name = $data['name'] ?? '';
        $this->surname = $data['surname'] ?? '';
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
}
