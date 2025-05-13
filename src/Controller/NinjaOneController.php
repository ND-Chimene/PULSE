<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_ADMIN')]
final class NinjaOneController extends AbstractController
{
    #[Route('/dashboard/ninjaOne', name: 'app_dashboard_ninja_one', methods: ['GET'])]
    public function index(): Response
    {
        return $this->render('dashboard/ninjaOne/index.html.twig', [
            'controller_name' => 'NinjaOneController',
        ]);
    }
}
