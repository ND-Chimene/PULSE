<?php

namespace App\Controller;

use App\Service\NinjaOneApiService;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[IsGranted('ROLE_IT')]
class DashboardController extends AbstractController
{
    private NinjaOneController $ninjaOneController;

    public function __construct(NinjaOneController $ninjaOneController)
    {
        $this->ninjaOneController = $ninjaOneController;
    }

    #[Route('/dashboard', name: 'app_dashboard', methods: ['GET'])]
    public function dashboard(): Response
    {
        // Redirection vers le tableau de bord de NinjaOne par défaut
        return $this->redirectToRoute('app_dashboard_ninjaOne');
    }

    // Affichage du tableau de bord NinjaOne avec les données récupérées
    #[Route('/dashboard/ninjaOne', name: 'app_dashboard_ninjaOne', methods: ['GET'])]
    public function index(): Response
    {
        $ninjaOneData = $this->ninjaOneController->getAllData();
        return $this->render('dashboard/ninjaOne/index.html.twig', [
            'ninjaOneData' => $ninjaOneData,
        ]);
    }
}
