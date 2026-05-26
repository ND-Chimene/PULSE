<?php

namespace App\MessageHandler;

use App\Message\SendEmailViaGraphMessage;
use App\Service\GraphMailService;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class SendEmailViaGraphMessageHandler
{
    public function __construct(
        private GraphMailService $graphMailService
    ) {}

    public function __invoke(SendEmailViaGraphMessage $message): void
    {
        $this->graphMailService->send(
            $message->to,
            $message->subject,
            $message->html
        );
    }
}