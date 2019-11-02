<?php

namespace App\Tests\Entity;

use PHPUnit\Framework\TestCase;
use App\Entity\BookChangeEvent;
use App\Exception\BookChangeEventException;
use App\Entity\Receiver;
use App\Entity\Book;

class BookChangeEventTest extends TestCase
{
    private $book;
    private $receiver;

    public function setUp()
    {
        $this->book = $this->createMock(Book::class);
        $this->book->method('__toString')
            ->will($this->returnValue('Lem Stanisław "Solaris"'));

        $this->receiver = $this->createMock(Receiver::class);
        $this->receiver->method('__toString')
            ->will($this->returnValue('Mazur Justyna'));
    }

    public function testReceiveBookEvent()
    {
        $event = new BookChangeEvent(
            BookChangeEvent::RECEIVE,
            3,
            new \DateTime('2019-05-17'),
            $this->book
        );
        $this->assertSame('17-05-2019: przyjęto 3 egz.', $event->__toString());
    }

    public function testItThrowsExcpetionIfEventNameIsInvalid()
    {
        $this->expectException(BookChangeEventException::class);
        $event = new BookChangeEvent(
            'invalid name',
            3,
            new \DateTime('2019-05-17'),
            $this->book
        );
    }
    
    /**
     * @dataProvider invalidNumbersOfBooksDataProvider()
     */
    public function testItThrowsExceptionWhenNumberOfBooksIsLessOrEqualThenZero(int $num)
    {
        $this->expectException(BookChangeEventException::class);
        $event = new BookChangeEvent(
            BookChangeEvent::RECEIVE,
            $num,
            new \DateTime('2019-05-17'),
            $this->book
        );
    }
    
    public function invalidNumbersOfBooksDataProvider(): array
    {
        return [
            [0],
            [-3],
        ];
    }

    public function testReleaseBooksEvent()
    {
        $event = new BookChangeEvent(
            BookChangeEvent::RELEASE,
            3,
            new \DateTime('2019-05-17'),
            $this->book,
            $this->receiver
        );
        $this->assertSame('17-05-2019: wydano 3 egz. Pobrał(a): Mazur Justyna', $event->__toString());
    }

    public function testSellBookEvent()
    {
        $event = new BookChangeEvent(
            BookChangeEvent::SELL,
            3,
            new \DateTime('2019-05-17'),
            $this->book
        );
        $this->assertSame('17-05-2019: sprzedano 3 egz.', $event->__toString());
    }

    public function testItCreatesTextFromReceiverPerspective()
    {
        $event = new BookChangeEvent(
            BookChangeEvent::RELEASE,
            3,
            new \DateTime('2019-05-19'),
            $this->book,
            $this->receiver
        );
        $this->assertSame(
            '19-05-2019 pobrał(a) 3 egz.: Lem Stanisław "Solaris"',
            $event->textFromReceiverPerspective()
        );
    }

    public function testItCreatesTextFromReceiverPerspectiveWithComment()
    {
        $event = new BookChangeEvent(
            BookChangeEvent::RELEASE,
            3,
            new \DateTime('2019-05-19'),
            $this->book,
            $this->receiver
        );
        $event->setComment('Testowy komentarz');
        $this->assertSame(
            '19-05-2019 pobrał(a) 3 egz.: Lem Stanisław "Solaris". Komentarz: Testowy komentarz',
            $event->textFromReceiverPerspective()
        );
    }
}

