<?php

namespace App\MessageHandler;

use App\Message\SendLoginEmailMessage;
use App\Repository\UserRepository;
use App\Service\GraphMailService;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Twig\Environment;

#[AsMessageHandler]
class SendLoginEmailMessageHandler
{
    public function __construct(
        private UserRepository $userRepository,
        private GraphMailService $graphMailService,
        private Environment $twig
    ) {}

    public function __invoke(SendLoginEmailMessage $message): void
    {
        $user = $this->userRepository->find($message->userId);

        if (!$user) {
            return;
        }

        $html = $this->twig->render('send_email/connexion.html.twig', [
            'user' => $user,
            'date' => $message->date,
            'ip_address' => $message->ipAddress,
            'device' => $message->device,
            'os' => $message->os,
            'browser' => $message->browser
        ]);

        $this->graphMailService->send(
            to: $user->getEmail(),
            subject: '🔐 Nouvelle connexion - RADARR',
            html: $html
        );
    }
}
