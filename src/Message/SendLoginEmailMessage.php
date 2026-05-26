<?php

namespace App\Message;

class SendLoginEmailMessage
{
    public function __construct(
        public int $userId,
        public string $date,
        public string $ipAddress,
        public string $device,
        public string $os,
        public string $browser
    ) {}
}