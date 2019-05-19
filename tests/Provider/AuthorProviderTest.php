<?php

namespace App\Tests\Provider;

use PHPUnit\Framework\TestCase;
use App\Provider\AuthorProvider;
use App\Repository\AuthorRepository;
use App\Entity\Author;

class AuthorProviderTest extends TestCase
{
    private $authorRepository;

    public function setUp()
    {
        $this->authorRepository = $this->createMock(AuthorRepository::class);
    }
    
    public function testItProvidesAllAuthorsOrderedBySurname()
    {
        $this->authorRepository->method('getAllOrderBySurname')
            ->will($this->returnValue([]));
            
        $authorProvider = new AuthorProvider($this->authorRepository);
        $this->assertSame([], $authorProvider->all());
    }

    public function testItProvidesAuthorByNameAndSurname()
    {
        $author = new Author('Fiodor', 'Dostojewski');
        $this->authorRepository->method('findOneByNameAndSurname')
            ->with(
                $this->equalTo('Fiodor'),
                $this->equalTo('Dostojewski'),
            )
            ->will($this->returnValue($author));
        $authorProvider = new AuthorProvider($this->authorRepository);
        $this->assertSame($author, $authorProvider->findOneByNameAndSurname('Fiodor', 'Dostojewski'));
    }
}

