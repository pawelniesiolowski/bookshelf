<?php

namespace App\Tests\Catalog\Provider;

use PHPUnit\Framework\TestCase;
use App\Catalog\Provider\AuthorProvider;
use App\Catalog\Repository\AuthorRepository;
use App\Catalog\Persistence\Author;

class AuthorProviderTest extends TestCase
{
    private $authorRepository;

    public function setUp(): void
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
                $this->equalTo('Dostojewski')
            )
            ->will($this->returnValue($author));
        $authorProvider = new AuthorProvider($this->authorRepository);
        $this->assertSame($author, $authorProvider->findOneByNameAndSurname('Fiodor', 'Dostojewski'));
    }
}

