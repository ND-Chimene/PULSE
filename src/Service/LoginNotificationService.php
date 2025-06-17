<?php

namespace App\Service;

use App\Entity\History;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\RequestStack;
use DeviceDetector\DeviceDetector;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Dom\Entity;

/**
 * Class LoginNotificationService
 * Ce service est responsable de l'envoi des notifications de connexion aux utilisateurs.
 * Il enregistre les détails de connexion et envoie un e-mail de notification lors d'une connexion réussie.
 */
class LoginNotificationService
{
    public function __construct(
        private MailerInterface $mailer,
        private RequestStack $requestStack
    ) {}

    public function sendLoginNotification(User $user, EntityManagerInterface $em): void
    {
        $request = $this->requestStack->getCurrentRequest();
        $deviceDetector = new DeviceDetector($request->headers->get('User-Agent'));
        $deviceDetector->parse();

        $ipAddress = $request->getClientIp();
        $device = $deviceDetector->getDeviceName();
        $os = $deviceDetector->getOs('name');
        $browser = $deviceDetector->getClient('name');
        $date = new \DateTime('now', new \DateTimeZone('Europe/Paris'));

        $history = new History();
        $history->setUser($user);
        $history->setIpAddress($ipAddress);
        $history->setDevice($device);
        $history->setOs($os);
        $history->setBrowser($browser);
        $history->setLoginDate($date);
        $em->persist($history);
        $em->flush();

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
