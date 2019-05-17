<?php

namespace App\Tests\Controller;

use App\Tests\FunctionalTestCase;
use App\Entity\Author;
use App\Entity\Book;

class BookControllerTest extends FunctionalTestCase
{
    public function testNew()
    {
        $client = static::createClient();
        $client->xmlHttpRequest('POST', '/books');
        $response = $client->getResponse();

        $this->assertSame(201, $response->getStatusCode());
    }
}

