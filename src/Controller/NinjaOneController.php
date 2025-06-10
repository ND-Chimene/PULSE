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

    // Importation du service NinjaOneApiService
    public function __construct(NinjaOneApiService $ninjaOneApiService)
    {
        $this->ninjaOneApiService = $ninjaOneApiService;
    }

    // Récupération des tickets selon leur état
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

    // Récupération des patches échoués et des logiciels rejetés
    private function getPatchesFailed(): array
    {
        $patchesFailed = $this->ninjaOneApiService->getPatchesFailed()["results"];
        $statusPatchesFailed = [];
        $patchesFailedCounts = [];

        foreach ($patchesFailed as $patch) {
            if (isset($patch['status'])) {
                $statusPatchesFailed[] = 'OS PATCH';
            }
        }

        if (!empty($statusPatchesFailed)) {
            $counts = array_count_values($statusPatchesFailed);
            $statusPatchesFailed = array_keys($counts);
            $patchesFailedCounts = array_values($counts);
        }

        return [
            'statusPatchesFailed' => $statusPatchesFailed,
            'patchesFailedCounts' => $patchesFailedCounts,
        ];
    }

    private function getSoftwaresRejected(): array
    {
        $softwaresRejected = $this->ninjaOneApiService->getSoftwaresRejected()["results"];
        $statusSoftwaresRejected = [];
        $softwaresRejectedCounts = [];

        foreach ($softwaresRejected as $software) {
            if (isset($software['status'])) {
                $statusSoftwaresRejected[] = 'SOFTWARE';
            }
        }

        if (!empty($statusSoftwaresRejected)) {
            $counts = array_count_values($statusSoftwaresRejected);
            $statusSoftwaresRejected = array_keys($counts);
            $softwaresRejectedCounts = array_values($counts);
        }

        return [
            'statusSoftwaresRejected' => $statusSoftwaresRejected,
            'softwaresRejectedCounts' => $softwaresRejectedCounts,
        ];
    }

    // Récupération des alertes, systèmes d'exploitation, antivirus et états de santé des appareils
    private function getAlerts(): array
    {
        $alerts = $this->ninjaOneApiService->getAlerts();
        $statusAlerts = [];
        $alertsCounts = [];

        $statusLabels = [
            "POSTES NON PATCHÉS (30j+)",
            "ESPACE DISQUE INSUFFISANT",
        ];

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

            foreach ($statusAlerts as $i => $label) {
                if (isset($statusLabels[$label])) {
                    $statusAlerts[$i] = $statusLabels[$label];
                }
            }
        }

        return [
            'alertsCounts' => $alertsCounts,
            'statusLabel' => $statusLabels,
        ];
    }

    private function getOperatingSystems(): array
    {
        $operatingSystems = $this->ninjaOneApiService->getOperatingSystems()["results"];
        $statusOS = [];
        $OSCounts = [];
        foreach ($operatingSystems as $os) {
            if (isset($os['needsReboot']) && $os['needsReboot'] === true) {
                $statusOS[] = "RÉDEMARRAGE NÉCESSAIRE";

            }
        }

        if (!empty($statusOS)) {
            $counts = array_count_values($statusOS);
            $statusOS = array_keys($counts);
            $OSCounts = array_values($counts);
        }

        return [
            'statusOS' => $statusOS,
            'OSCounts' => $OSCounts,
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

    // Récupération de toutes les données pour le tableau de bord
    public function getAllData(): array
    {
        return [
            'tickets' => $this->getTickets(),
            'allPatches' => array_sum($this->getPatchesFailed()['patchesFailedCounts']) + array_sum($this->getSoftwaresRejected()['softwaresRejectedCounts']),
            'allPatchesJson' => json_encode(array_merge($this->getPatchesFailed()['statusPatchesFailed'], $this->getSoftwaresRejected()['statusSoftwaresRejected'])),
            'allPatchesCountsJson' => json_encode(array_merge($this->getPatchesFailed()['patchesFailedCounts'], $this->getSoftwaresRejected()['softwaresRejectedCounts'])),
            'patchesFailed' => $this->getPatchesFailed(),
            'softwaresRejected' => $this->getSoftwaresRejected(),
            'operatingSystems' => $this->getOperatingSystems(),
            'alerts' => $this->getAlerts(),
            'allAlerts' => array_sum($this->getAlerts()['alertsCounts']) + array_sum($this->getOperatingSystems()['OSCounts']),
            'allAlertsJson' => json_encode(array_merge($this->getAlerts()['alertsCounts'], $this->getOperatingSystems()['OSCounts'])),
            'allLabelsJson' => json_encode(array_merge($this->getAlerts()['statusLabel'], $this->getOperatingSystems()['statusOS'])),
            'antivirus' => $this->getAntivirus(),
            'deviceHealths' => $this->getDeviceHealths(),
        ];
    }

    // Route pour afficher le tableau de bord NinjaOne
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
