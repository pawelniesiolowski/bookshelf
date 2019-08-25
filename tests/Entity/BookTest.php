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
    private $receiver;

    public function setUp()
    {
        $this->receiver = $this->createMock(Receiver::class);
        $this->receiver->method('__toString')
            ->will($this->returnValue('Mazur Justyna'));
    }

    public function testItCanBeJsonSerialized()
    {
        $book = new Book('Bracia Karamazow');
        $book->setISBN('0123456789');
        $book->setPrice(29.99);
        $book->addAuthor(new Author('Fiodor', 'Dostojewski'));
        $book->addAuthor(new Author('Michaił', 'Bułhakow'));

        $jsonSerializeData = [
            'id' => null,
            'title' => 'Bracia Karamazow',
            'ISBN' => '0123456789',
            'price' => 29.99,
            'copies' => 0,
            'authors' => [
                [
                    'name' => 'Fiodor',
                    'surname' => 'Dostojewski',
                ],
                [
                    'name' => 'Michaił',
                    'surname' => 'Bułhakow',
                ],
            ],
        ];
        $this->assertSame(true, $book->validate());
        $this->assertSame($jsonSerializeData, $book->jsonSerialize());
    }

    public function testDefaultValues()
    {
        $book = new Book('Bracia Karamazow');
        $jsonSerializeData = [
            'id' => null,
            'title' => 'Bracia Karamazow',
            'ISBN' => '',
            'price' => 0.0,
            'copies' => 0,
            'authors' => [],
        ];
        $this->assertSame(true, $book->validate());
        $this->assertSame($jsonSerializeData, $book->jsonSerialize());
    }

    public function testItShouldCreateJsonSerializedExtendedData()
    {
        $book = new Book('Bracia Karamazow');
        $book->setISBN('0123456789');
        $book->setPrice(29.99);
        $book->addAuthor(new Author('Fiodor', 'Dostojewski'));
        $jsonSerializeData = [
            'id' => null,
            'title' => 'Bracia Karamazow',
            'ISBN' => '0123456789',
            'price' => 29.99,
            'copies' => 0,
            'authors' => [
                [
                    'name' => 'Fiodor',
                    'surname' => 'Dostojewski',
                ],
            ],
            'events' => [],
        ];
        $this->assertSame(true, $book->validate());
        $this->assertSame($jsonSerializeData, $book->jsonSerializeExtended());
    }

    public function testItCreatesJsonSerializedBasicData()
    {
        $book = new Book('Bracia Karamazow');
        $jsonSerializedBasicData = [
            'id' => null,
            'title' => 'Bracia Karamazow',
            'copies' => 0,
        ];
        $this->assertSame(true, $book->validate());
        $this->assertSame($jsonSerializedBasicData, $book->jsonSerializeBasic());
    }

    public function testItCanBeUsedAsString()
    {
        $book = new Book('Bracia Karamazow');
        $book->addAuthor(new Author('Fiodor', 'Dostojewski'));
        $this->assertSame(true, $book->validate());
        $this->assertSame('Dostojewski Fiodor "Bracia Karamazow"', $book->__toString());
    }

    public function testReceiveBook()
    {
        $book = new Book('Bracia Karamazow');
        $book->receive(1);
        $this->assertSame(true, $book->validate());
        $jsonSerializedBook = $book->jsonSerializeExtended();
        $this->assertSame(1, $jsonSerializedBook['copies']);
        $this->assertContains('przyjęto', $jsonSerializedBook['events'][0]); 
    }

    public function testReleaseBooks()
    {
        $book = new Book('Bracia Karamazow');
        $book->receive(5);
        $book->release(3, $this->receiver);
        $jsonSerializedBook = $book->jsonSerializeExtended();
        $this->assertSame(true, $book->validate());
        $this->assertSame(2, $jsonSerializedBook['copies']);
        $this->assertSame(2, count($jsonSerializedBook['events']));
        $this->assertContains('wydano', $jsonSerializedBook['events'][1]);
        $this->assertContains('Pobrał(a): Mazur Justyna', $jsonSerializedBook['events'][1]);
    }
    
    public function testSellBooks()
    {
        $book = new Book('Bracia Karamazow');
        $book->receive(5);
        $event = $book->sell(2);
        $this->assertSame(true, $book->validate());
        $jsonSerializedBook = $book->jsonSerializeExtended();
        $this->assertSame(3, $jsonSerializedBook['copies']);
        $this->assertSame(2, count($jsonSerializedBook['events']));
        $this->assertContains('sprzedano', $jsonSerializedBook['events'][1]); 
    }

    public function testItShouldThrowExceptionWhenThereAreLessThenZeroBooks()
    {
        $book = new Book('Bracia Karamazow');
        $book->receive(5);
        $this->expectException(BookException::class);
        $event = $book->release(6, $this->receiver);
    }

    public function testItShouldThrowExceptionWhenReceiveMethodGetsLessThenZero()
    {
        $book = new Book('Bracia Karamazow');
        $this->expectException(BookException::class);
        $book->receive(-5);
    }

    public function testItShouldValidateItselfAndReturnProperErrors()
    {
        $book = new Book('');
        $book->setISBN('invalid');
        $book->setPrice(100000);
        $book->addAuthor(new Author('', ''));
        $this->assertSame(false, $book->validate());
        $errors = $book->getErrors();

        $this->assertSame('Podaj tytuł', $errors['title']);
        $this->assertSame('ISBN musi się składać tylko z cyfr, myślników i znaków "X"', $errors['ISBN']);
        $this->assertSame('Podaj imię autora', $errors['authors'][0]['authorName']);
        $this->assertSame('Podaj nazwisko autora', $errors['authors'][0]['authorSurname']);
        $this->assertSame('Cena książki nie może być wyższa niż 99999.00 zł', $errors['price']);
    }

    public function testItShouldUpdateItselfFromJson()
    {
        $book = new Book('Fiodor Dostojewski');
        $book->setISBN('0123456789');
        $book->setPrice(19.99);
        $book->addAuthor(new Author('Stanisław', 'Lem'));

        $bookFromRequest = [
            'title' => 'Zbrodnia i kara',
            'ISBN' => '1234567890',
            'price' => 39.99,
            'authors' => [
                [
                    'name' => 'Fiodor',
                    'surname' => 'Dostojewski',
                ],
            ],
        ];
        $authors = [new Author('Fiodor', 'Dostojewski')];
        $bookFromRequest = json_encode($bookFromRequest);
        $book->updateFromJson($bookFromRequest, $authors);

        $jsonSerializeData = [
            'id' => null,
            'title' => 'Zbrodnia i kara',
            'ISBN' => '1234567890',
            'price' => 39.99,
            'copies' => 0,
            'authors' => [
                [
                    'name' => 'Fiodor',
                    'surname' => 'Dostojewski',
                ],
            ],
            'events' => [],
        ];
        $this->assertSame($jsonSerializeData, $book->jsonSerializeExtended());
    }

    public function testISBNWithTenDigitsShouldBeValid()
    {
        $book = new Book('Testing ISBN');
        $book->setISBN('1234567890');
        $this->assertSame(true, $book->validate());
    }

    public function testISBNWithThirteenDigitsShouldBeValid()
    {
        $book = new Book('Testing ISBN');
        $book->setISBN('1234567890123');
        $this->assertSame(true, $book->validate());
    }

    public function testISBNWithTwelveDigitsAndXAndFourDashesShouldBeValid()
    {
        $book = new Book('Testing ISBN');
        $book->setISBN('123-45-678901-2-X');
        $book->validate();
        $this->assertSame(true, $book->validate());
    }

    public function testItShouldEmitErrorWhenISBNHasInvalidNumberOfDigitsOrX()
    {
        $book = new Book('Testing ISBN');
        $book->setISBN('123456789');
        $this->assertSame(false, $book->validate());
        $book->setISBN('12345678901');
        $this->assertSame(false, $book->validate());
        $book->setISBN('123456789X1234');
        $this->assertSame(false, $book->validate());
        $book->setISBN('123456789012345678');
        $this->assertSame(false, $book->validate());
    }

    public function testISBNShouldHaveOnlyNumbersDashesAndX()
    {
        $book = new Book('Testing ISBN');
        $book->setISBN('123456789012a');
        $this->assertSame(false, $book->validate());
    }
}

