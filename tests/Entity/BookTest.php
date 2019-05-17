<?php

namespace App\Tests\Entity;

use PHPUnit\Framework\TestCase;
use App\Entity\Book;
use App\Entity\Author;
use App\Entity\BookChangeEvent;
use App\Exception\BookException;
use App\Entity\Receiver;

class BookTest extends TestCase
{
    private $author;
    private $receiver;

    public function setUp()
    {
        $this->author = $this->createMock(Author::class);
        $this->author->method('__toString')
            ->will($this->returnValue('Dostojewski Fiodor'));

        $this->receiver = $this->createMock(Receiver::class);
        $this->receiver->method('__toString')
            ->will($this->returnValue('Mazur Justyna'));
    }

    public function testItCanBeJsonSerialized()
    {
        $book = new Book('Bracia Karamazow', '0123456789', 29.99);
        $book->addAuthor($this->author);
        $jsonSerializeData = [
            'id' => null,
            'title' => 'Bracia Karamazow',
            'ISBN' => '0123456789',
            'price' => 29.99,
            'copies' => 0,
            'authors' => ['Dostojewski Fiodor'],
            'events' => [],
        ];
        $this->assertSame($jsonSerializeData, $book->jsonSerialize());
    }

    public function testItCreatesJsonSerializedBasicData()
    {
        $book = new Book('Bracia Karamazow', '0123456789', 29.99);
        $book->addAuthor($this->author);
        $jsonSerializedBasicData = [
            'id' => null,
            'title' => 'Bracia Karamazow',
            'copies' => 0,
        ];
        $this->assertSame($jsonSerializedBasicData, $book->jsonSerializeBasic());
    }

    public function testItCanBeUsedAsString()
    {
        $book = new Book('Bracia Karamazow', '0123456789', 29.99);
        $book->addAuthor($this->author);
        $this->assertSame('Dostojewski Fiodor "Bracia Karamazow"', $book->__toString());
    }

    public function testReceiveBook()
    {
        $book = new Book('Bracia Karamazow', '0123456789', 29.99);
        $book->receive(1);
        $jsonSerializedBook = $book->jsonSerialize();
        $this->assertSame(1, $jsonSerializedBook['copies']);
        $this->assertContains('przyjęto', $jsonSerializedBook['events'][0]); 
    }

    public function testReleaseBooks()
    {
        $book = new Book('Bracia Karamazow', '0123456789', 29.99);
        $book->receive(5);
        $book->release(3, $this->receiver);
        $jsonSerializedBook = $book->jsonSerialize();
        $this->assertSame(2, $jsonSerializedBook['copies']);
        $this->assertSame(2, count($jsonSerializedBook['events']));
        $this->assertContains('wydano', $jsonSerializedBook['events'][1]);
        $this->assertContains('Pobrał(a): Mazur Justyna', $jsonSerializedBook['events'][1]);
    }
    
    public function testSellBooks()
    {
        $book = new Book('Bracia Karamazow', '0123456789', 29.99);
        $book->receive(5);
        $event = $book->sell(2);
        $jsonSerializedBook = $book->jsonSerialize();
        $this->assertSame(3, $jsonSerializedBook['copies']);
        $this->assertSame(2, count($jsonSerializedBook['events']));
        $this->assertContains('sprzedano', $jsonSerializedBook['events'][1]); 
    }

    public function testBookThrowExceptionWhenThereAreLessThenZeroBooks()
    {
        $book = new Book('Bracia Karamazow', '0123456789', 29.99);
        $book->receive(5);
        $this->expectException(BookException::class);
        $event = $book->release(6, $this->receiver);
    }
}

