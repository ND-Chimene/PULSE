<?php

namespace App\Controller;

use App\Service\NinjaOneApiService;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

#[IsGranted('ROLE_ADMIN')]

class NinjaOneController extends AbstractController
{
    private NinjaOneApiService $ninjaOneApiService;

    public function __construct(NinjaOneApiService $ninjaOneApiService)
    {
        $this->ninjaOneApiService = $ninjaOneApiService;
    }


    private function getTickets(): array
    {
        $tickets = $this->ninjaOneApiService->getTickets();

        $statusTickets = [];
        $ticketCounts = [];
        $titleStatusTickets = ['Tickets non attribués', 'Tickets ouverts', 'Tickets Non Résolus'];

        foreach ($tickets as $ticket) {
            if (isset($ticket['name']) && in_array($ticket['name'], $titleStatusTickets)) {
                $statusTickets[] = $ticket['name'];
                $ticketCounts[] = (int)($ticket['ticketCount'] ?? 0);
            }
        }
        $openTicketCounts = array_sum(array_slice($ticketCounts, 1, 3));

        return [
            'statusTickets' => $statusTickets,
            'ticketCounts' => $ticketCounts,
            'statusTicketsJson' => json_encode($statusTickets),
            'ticketCountsJson' => json_encode($ticketCounts),
            'openTicketCounts' => $openTicketCounts,
        ];
    }

    private function getPatches(): array
    {
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

        $unpatchesCounts = $patchesCounts[0];

        return [
            'statusPatches' => $statusPatches,
            'patchesCounts' => $patchesCounts,
            'statusPatchesJson' => json_encode($statusPatches),
            'patchesCountsJson' => json_encode($patchesCounts),
            'unpatchesCounts' => $unpatchesCounts,
        ];
    }

    private function getAlerts(): array
    {
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

        $sallAlerts = array_sum($alertsCounts);

        return [
            'statusAlerts' => $statusAlerts,
            'alertsCounts' => $alertsCounts,
            'statusAlertsJson' => json_encode($statusAlerts),
            'alertsCountsJson' => json_encode($alertsCounts),
            'allAlerts' => $sallAlerts,
        ];
    }

    private function getAntivirus(): array
    {
        $antivirus = $this->ninjaOneApiService->getAntivirus()["results"];
        $statusAntivirus = [];
        foreach ($antivirus as $antivirus) {
            if (isset($antivirus['name'])) {
                $status = $antivirus['name'];
                $statusAntivirus[] = $status;
            }
        }
        if (!empty($statusAntivirus)) {
            $counts = array_count_values($statusAntivirus);
            $statusAntivirus = array_keys($counts);
        }

        return [
            'statusAntivirus' => $statusAntivirus,
        ];
    }

    private function getDeviceHealths(): array
    {
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

        return [
            'statusHealth' => $statusHealth,
            'healthCounts' => $healthCounts,
            'statusHealthJson' => json_encode($statusHealth),
            'healthCountsJson' => json_encode($healthCounts),
        ];
    }

    public function getAllData(): array
    {
        return [
            'tickets' => $this->getTickets(),
            'patches' => $this->getPatches(),
            'alerts' => $this->getAlerts(),
            'antivirus' => $this->getAntivirus(),
            'deviceHealths' => $this->getDeviceHealths(),
        ];
    }

    #[Route('/dashboard/ninjaOne', name: 'app_dashboard_ninjaOne', methods: ['GET'])]
    public function index(): Response
    {
        $ninjaOneData = $this->getAllData();
        return $this->render('dashboard/ninjaOne/index.html.twig', [
            'ninjaOneData' => $ninjaOneData,
            'title' => 'NinjaOne',
        ]);
    }
}
