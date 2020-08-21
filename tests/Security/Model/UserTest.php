<?php

namespace App\Tests\Security\Model;

use PHPUnit\Framework\TestCase;
use App\Security\Model\User;

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

