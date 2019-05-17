<?php

namespace App\Tests\Provider;

use PHPUnit\Framework\TestCase;
use App\Provider\AuthorProvider;
use App\Repository\AuthorRepository;

class AuthorProviderTest extends TestCase
{
    public function testItProvidesAllAuthorsOrderedBySurname()
    {
        $authorRepository = $this->createMock(AuthorRepository::class);
        $authorRepository->method('getAllOrderBySurname')
            ->will($this->returnValue([]));
            
        $authorProvider = new AuthorProvider($authorRepository);
        $this->assertInternalType('array', $authorProvider->all());
    }
}

