<?php

namespace App\Receiver\Factory;

use App\Receiver\Model\Receiver;

class ReceiverFactory
{
    public function fromJson(string $data): Receiver
    {
        $data = json_decode($data, true);
        return new Receiver(
            $data['name'] ?? '',
            $data['surname'] ?? ''
        );
    }
}
