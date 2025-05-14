<?php

namespace App\Controller;

use App\Service\NinjaOneApiService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[IsGranted('ROLE_ADMIN')]
final class NinjaOneController extends AbstractController
{
        #[Route('/ninjaone/tickets', name: 'app_ninja_one_tickets', methods: ['GET'])]
    public function getTicketS(Request $request, NinjaOneApiService $ninjaOneApiService): Response
    {

        $clientId = "ninjaOneApiClientId";
        $clientSecret = 'ninjaOneApiClientSecret';

        $ninjaOneApiService->authenticate($clientId, $clientSecret);
        $tickets = $ninjaOneApiService->getTickets();

        return $this->render('dashboard/ninjaOne/tickets.html.twig', [
            'tickets' => $tickets,
        ]);
    }

        #[Route('/ninjaone/patches', name: 'app_ninja_one_patches', methods: ['GET'])]
    public function getPatches(Request $request, NinjaOneApiService $ninjaOneApiService): Response
    {

        $clientId = "ninjaOneApiClientId";
        $clientSecret = 'ninjaOneApiClientSecret';

        $ninjaOneApiService->authenticate($clientId, $clientSecret);
        $patches = $ninjaOneApiService->getPatches();

        return $this->render('dashboard/ninjaOne/patches.html.twig', [
            'patches' => $patches,
        ]);
    }

        #[Route('/ninjaone/alerts', name: 'app_ninja_one_alerts', methods: ['GET'])]
    public function getAlerts(Request $request, NinjaOneApiService $ninjaOneApiService): Response
    {

        $clientId = "ninjaOneApiClientId";
        $clientSecret = 'ninjaOneApiClientSecret';

        $ninjaOneApiService->authenticate($clientId, $clientSecret);
        $alerts = $ninjaOneApiService->getAlerts();

        return $this->render('dashboard/ninjaOne/alerts.html.twig', [
            'alerts' => $alerts,
        ]);
    }

        #[Route('/ninjaone/vulnerabilities', name: 'app_ninja_one_vulnerabilities', methods: ['GET'])]
    public function getVulnerabilities(Request $request, NinjaOneApiService $ninjaOneApiService): Response
    {

        $clientId = "ninjaOneApiClientId";
        $clientSecret = 'ninjaOneApiClientSecret';

        $ninjaOneApiService->authenticate($clientId, $clientSecret);
        $vulnerabilities = $ninjaOneApiService->getVulnerabilities();

        return $this->render('dashboard/ninjaOne/vulnerabilities.html.twig', [
            'vulnerabilities' => $vulnerabilities,
        ]);
    }

        #[Route('/ninjaone/devices', name: 'app_ninja_one_devices', methods: ['GET'])]
    public function getDevices(Request $request, NinjaOneApiService $ninjaOneApiService): Response
    {

        $clientId = "ninjaOneApiClientId";
        $clientSecret = 'ninjaOneApiClientSecret';

        $ninjaOneApiService->authenticate($clientId, $clientSecret);
        $devices = $ninjaOneApiService->getDeviceHealths();

        return $this->render('dashboard/ninjaOne/s.html.twig', [
            'devices' => $devices,
        ]);
    }
}
