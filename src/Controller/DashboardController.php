<?php

namespace App\Controller;

use App\Service\NinjaOneApiService;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[IsGranted('ROLE_ADMIN')]
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

            // All Tickets
            $tickets = $this->ninjaOneApiService->getTickets();
            $statusTickets = [];
            $ticketCounts = [];
            $desiredTickets = ['Tickets non attribués', 'Tickets ouverts', 'Tickets Non Résolus'];

            foreach ($tickets as $ticket) {
                if (isset($ticket['name']) && in_array($ticket['name'], $desiredTickets)) {
                    $statusTickets[] = $ticket['name'];
                    $ticketCounts[] = (int)($ticket['ticketCount'] ?? 0);
                }
            }

            // All Open Tickets
            $openTickets = array_slice($statusTickets, 1, 3);
            $openTicketCounts = array_sum(array_slice($ticketCounts, 1, 3));

            // All OS Patches
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

            // All Unpatches
            $unpatches = array_slice($statusPatches, 0, 2);
            $unpatchesCounts = array_sum(array_slice($patchesCounts, 0, 2));

            // All Alerts
            $alerts = $this->ninjaOneApiService->getAlerts();
            $statusAlerts = [];
            $alertsCounts = [];
            foreach ($alerts as $alert) {
                if (isset($alert['sourceType'])) {
                    $source = $alert['sourceType'];
                    $statusAlerts[] = $source;
                }
            }
            if (!empty($statusAlerts)) {
                $counts = array_count_values($statusAlerts);
                $statusAlerts = array_keys($counts);
                $alertsCounts = array_values($counts);
            }

            // All Antivirus
            $antivirus = $this->ninjaOneApiService->getAntivirus()["results"];
            $statusAntivirus = [];
            $antivirusCounts = [];
            foreach ($antivirus as $antivirus) {
                if (isset($antivirus['name'])) {
                    $name = $antivirus['name'];
                    $statusAntivirus[] = $name;
                }
            }
            if (!empty($statusAntivirus)) {
                $counts = array_count_values($statusAntivirus);
                $statusAntivirus = array_keys($counts);
                $antivirusCounts = array_values($counts);
            }


            // All Devices Healths
            $deviceHealths = $this->ninjaOneApiService->getDeviceHealths()["results"];
            $statusHealth = [];
            $healthCounts = [];
            foreach ($deviceHealths as $deviceHealth) {
                if (isset($deviceHealth['healthStatus'])) {
                    $status = $deviceHealth['healthStatus'];
                    $statusHealth[] = $status;
                }
            }
            if (!empty($statusHealth)) {
                $counts = array_count_values($statusHealth);
                $statusHealth = array_keys($counts);
                $healthCounts = array_values($counts);
            }

            return $this->render('dashboard/index.html.twig', [
                'statusTickets' => $statusTickets,
                'ticketCounts' => $ticketCounts,
                'openTickets' => $openTickets,
                'openTicketCounts' => $openTicketCounts,
                'statusPatches' => $statusPatches,
                'patchesCounts' => $patchesCounts,
                'unpatches' => $unpatches,
                'unpatchesCounts' => $unpatchesCounts,
                'statusAlerts' => $statusAlerts,
                'alertsCounts' => $alertsCounts,
                'statusAntivirus' => $statusAntivirus,
                'antivirusCounts' => $antivirusCounts,
                'statusHealth' => $statusHealth,
                'healthCounts' => $healthCounts,
            ]);
        } catch (\RuntimeException $e) {
            $this->addFlash('error', 'Erreur de connexion à NinjaOne: ' . $e->getMessage());
            return $this->redirectToRoute('app_login');
        }
    }
}
