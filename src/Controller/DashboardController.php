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

            // Tickets
            $tickets = $this->ninjaOneApiService->getTickets();
            $statusTickets = [];
            $ticketCounts = [];
            foreach ($tickets as $ticket) {
                if (isset($ticket['displayName'])) {
                    $status = $ticket['displayName'];
                    $statusTickets[] = $status;
                }
            }
            $statusTickets = array_slice($statusTickets, 0, 4);
            if (!empty($statusTickets)) {
                $counts = array_count_values($statusTickets);
                $statusTickets = array_keys($counts);
                $ticketCounts = array_values($counts);
            }

            // Patches
            $patches = $this->ninjaOneApiService->getPatches()["results"];
            $statusPatches = [];
            $patchesCounts = [];
            foreach ($patches as $patch) {
                if (isset($patch['status'])) {
                    $status = $patch['status'];
                    $statusPatches[] = $status;
                }
            }
            if (!empty($statusPatches)) {
                $counts = array_count_values($statusPatches);
                $statusPatches = array_keys($counts);
                $patchesCounts = array_values($counts);
            }

            // Alerts
            $alerts = $this->ninjaOneApiService->getAlerts();

            // Vulnerabilities
            $vulnerabilities = $this->ninjaOneApiService->getVulnerabilities();

            // Device Healths
            $deviceHealths = $this->ninjaOneApiService->getDeviceHealths();

            return $this->render('dashboard/index.html.twig', [
                'statusTickets' => $statusTickets,
                'ticketCounts' => $ticketCounts,
                'statusPatches' => $statusPatches,
                'patchesCounts' => $patchesCounts,
                'alerts' => $alerts,
                'vulnerabilities' => $vulnerabilities,
                'deviceHealths' => $deviceHealths["results"],
            ]);
        } catch (\RuntimeException $e) {
            $this->addFlash('error', 'Erreur de connexion à NinjaOne: ' . $e->getMessage());
            return $this->redirectToRoute('app_login');
        }
    }
}
