<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity(repositoryClass="App\Repository\BookRepository")
 */
class Book
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
    private $copies;
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

    public function __construct(
        string $title,
        int $copies,
        string $ISBN,
        float $price
    ) {
        $this->title = $title;
        $this->copies = $copies;
        $this->ISBN = $ISBN;
        $this->price = $price;
        $this->authors = new ArrayCollection();
    }

    public function addAuthor(Author $author)
    {
        if (!$this->authors->contains($author)) {
            $this->authors[] = $author;
            $author->addBook($this);
        }
    }

    public function __toString()
    {
        return $this->title;
    }
    
    public function jsonSimplify(): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'copies' => $this->copies,
        ];
    }
}

