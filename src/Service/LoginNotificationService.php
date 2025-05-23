<?php

namespace App\Service;

use Symfony\Component\Mailer\MailerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\RequestStack;
use DeviceDetector\DeviceDetector;
use App\Entity\User;

class LoginNotificationService
{
    public function __construct(
        private MailerInterface $mailer,
        private RequestStack $requestStack
    ) {}

    public function sendLoginNotification(User $user): void
    {
        $request = $this->requestStack->getCurrentRequest();
        $deviceDetector = new DeviceDetector($request->headers->get('User-Agent'));
        $deviceDetector->parse();

        $ipAddress = $request->getClientIp();
        $device = $deviceDetector->getDeviceName();
        $os = $deviceDetector->getOs('name');
        $browser = $deviceDetector->getClient('name');
        $date = new \DateTime('now', new \DateTimeZone('Europe/Paris'));

        $email = (new TemplatedEmail())
            ->from('noreply@pulse.fr')
            ->to($user->getEmail())
            ->subject('Nouvelle connexion détectée')
            ->htmlTemplate('send_email/index.html.twig')
            ->context([
                'user' => $user,
                'date' => $date->format('Y-m-d H:i:s'),
                'ip_address' => $ipAddress,
                'device' => $device,
                'os' => $os,
                'browser' => $browser,
            ]);

        $this->mailer->send($email);
    }
}
