<?php

namespace App\Tests\Shared\Entity;

use PHPUnit\Framework\TestCase;
use App\Shared\Persistence\User;

class UserTest extends TestCase
{
    public function testItCanBeUsedAsString()
    {
        $user = new User(
            'test@example.com',
            'test',
            'Jan',
            'Nowak'
        );
        $this->assertSame('Jan Nowak', '' . $user);
    }
}

