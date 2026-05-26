<?php

namespace App\Message;

class SendEmailViaGraphMessage
{
    public function __construct(
        public string $to,
        public string $subject,
        public string $html,
    ) {}
}