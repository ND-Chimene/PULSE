<?php

namespace App\EventListener;

use Symfony\Component\Security\Http\Event\LoginSuccessEvent;
use App\Service\LoginNotificationService;

class LoginSuccessListener
{
    public function __construct(
        private LoginNotificationService $notificationService
    ) {}

    public function __invoke(LoginSuccessEvent $event): void
    {
        $user = $event->getUser();
        if (method_exists($user, 'getEmail')) {
            $this->notificationService->sendLoginNotification($user);
        }
    }
}
