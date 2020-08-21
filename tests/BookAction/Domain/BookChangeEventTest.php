<?php

namespace App\Tests\BookAction\Domain;

use PHPUnit\Framework\TestCase;
use App\BookAction\Domain\BookChangeEvent;
use App\BookAction\Domain\Exception\BookChangeEventException;
use Ramsey\Uuid\Uuid;

class BookChangeEventTest extends TestCase
{
    public function testReceiveBookEvent()
    {
        $event = new BookChangeEvent(
            BookChangeEvent::RECEIVE,
            3,
            new \DateTime('2019-05-17'),
            Uuid::uuid1()->toString(),
            'Solaris'
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
            Uuid::uuid1()->toString(),
            'Solaris'
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
            Uuid::uuid1()->toString(),
            'Solaris'
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
            Uuid::uuid1()->toString(),
            'Solaris',
            'Lem Stanisław',
            Uuid::uuid1()->toString(),
            'Mazur Justyna'
        );
        $this->assertSame('17-05-2019: wydano 3 egz. Pobrał(a): Mazur Justyna', $event->__toString());
    }

    public function testReleaseBooksEventWithComment()
    {
        $event = new BookChangeEvent(
            BookChangeEvent::RELEASE,
            3,
            new \DateTime('2019-05-17'),
            Uuid::uuid1()->toString(),
            'Solaris',
            'Lem Stanisław',
            Uuid::uuid1()->toString(),
            'Mazur Justyna'
        );
        $event->setComment('Testowy komentarz');
        $this->assertSame('17-05-2019: wydano 3 egz. Pobrał(a): Mazur Justyna. Komentarz: Testowy komentarz', $event->__toString());
    }

    public function testSellBookEvent()
    {
        $event = new BookChangeEvent(
            BookChangeEvent::SELL,
            3,
            new \DateTime('2019-05-17'),
            Uuid::uuid1()->toString(),
            'Solaris'
        );
        $this->assertSame('17-05-2019: sprzedano 3 egz.', $event->__toString());
    }

    public function testItCreatesTextFromReceiverPerspective()
    {
        $event = new BookChangeEvent(
            BookChangeEvent::RELEASE,
            3,
            new \DateTime('2019-05-19'),
            Uuid::uuid1()->toString(),
            'Solaris',
            Uuid::uuid1()->toString(),
            'Mazur Justyna'
        );
        $this->assertSame(
            '19-05-2019 pobrał(a) 3 egz.: "Solaris"',
            $event->textFromReceiverPerspective()
        );
    }

    public function testItCreatesTextFromReceiverPerspectiveWithComment()
    {
        $event = new BookChangeEvent(
            BookChangeEvent::RELEASE,
            3,
            new \DateTime('2019-05-19'),
            Uuid::uuid1()->toString(),
            'Solaris',
            Uuid::uuid1()->toString(),
            'Mazur Justyna'
        );
        $event->setComment('Testowy komentarz');
        $this->assertSame(
            '19-05-2019 pobrał(a) 3 egz.: "Solaris". Komentarz: Testowy komentarz',
            $event->textFromReceiverPerspective()
        );
    }
}

