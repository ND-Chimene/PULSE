<?php

namespace App\Controller;

use App\Service\NinjaOneApiService;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class DashboardController extends AbstractController
{
    private NinjaOneApiService $ninjaOneApiService;

    public function __construct(NinjaOneApiService $ninjaOneApiService)
    {
        $this->ninjaOneApiService = $ninjaOneApiService;
    }

    #[Route('/dashboard', name: 'app_dashboard', methods: ['GET'])]
    public function index(): Response
    {
        try {
            if (!$this->ninjaOneApiService->hasValidTokens()) {
                $this->ninjaOneApiService->authenticate();
            }

            $tickets = $this->ninjaOneApiService->getTickets();
            $patches = $this->ninjaOneApiService->getPatches();
            $alerts = $this->ninjaOneApiService->getAlerts();
            $vulnerabilities = $this->ninjaOneApiService->getVulnerabilities();
            $deviceHealths = $this->ninjaOneApiService->getDeviceHealths();

            return $this->render('dashboard/index.html.twig', [
                'tickets' => $tickets,
                'patches' => $patches,
                'alerts' => $alerts,
                'vulnerabilities' => $vulnerabilities,
                'deviceHealths' => $deviceHealths,
            ]);
        } catch (\RuntimeException $e) {
            $this->addFlash('error', 'Erreur de connexion à NinjaOne: ' . $e->getMessage());
            return $this->redirectToRoute('app_login');
        }
    }
}
