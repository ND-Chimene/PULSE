<?php

namespace App\Controller;

use App\Repository\HistoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_ADMIN')]
// Affichage du journal des connexions
// Accessible uniquement aux utilisateurs ayant le rôle ROLE_ADMIN
final class HistoryController extends AbstractController
{
    #[Route('/history', name: 'app_history_log')]
    public function index(HistoryRepository $historyRepository): Response
    {
        $historyEntities = $historyRepository->allHistory();
        $history = [];
        foreach ($historyEntities as $item) {
            $history[] = [
                'loginDate' => $item->getLoginDate()?->format('d/m/Y H:i:s'),
                'ipAddress' => $item->getIpAddress(),
                'device' => $item->getDevice(),
                'os' => $item->getOs(),
                'browser' => $item->getBrowser(),
                'userId' => $item->getUser()->getId(),
            ];
        }
        return $this->render('history/index.html.twig', [
            'title' => 'Journal des connexions',
            'history' => $history,
        ]);
    }
}
