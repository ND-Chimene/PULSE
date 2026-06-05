<?php

namespace App\EventListener;

use Doctrine\ORM\EntityManagerInterface;
use App\Service\LoginNotificationService;
use Symfony\Component\Security\Http\Event\LoginSuccessEvent;

/**
 * Class LoginSuccessListener
 * Ce listener gère les événements de connexion réussie et envoie un e-mail de notification.
 */
class LoginSuccessListener
{
    public function __construct(
        private LoginNotificationService $notificationService,
        private EntityManagerInterface $em
    ) {}

    public function __invoke(LoginSuccessEvent $event): void
    {
        $user = $event->getUser();
        if (method_exists($user, 'getEmail')) {
            $this->notificationService->sendLoginNotification($user, $this->em);
        }
    }
}
