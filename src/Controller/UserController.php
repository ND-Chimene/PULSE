<?php

namespace App\Controller;

use App\Form\UserForm;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

//Page de gestion du profil utilisateur
#[IsGranted('ROLE_IT')]
final class UserController extends AbstractController
{
    public function __construct(private EntityManagerInterface $em) {}
    #[Route('/settings', name: 'app_profile', methods: ['GET', 'POST'])]
    public function index(Request $request, UserPasswordHasherInterface $ph): Response
    {
        $user = $this->getUser();
        $form = $this->createForm(UserForm::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Comparaison du mot de passe
            if ($ph->isPasswordValid($user, $form->get('password')->getData())) {
                $this->em->flush();
                $this->addFlash('success', 'Votre profil a été mis à jour.');
                return $this->redirectToRoute('app_profile');
            } else {
                // Mot de passe incorrect : on remet les valeurs initiales
                $this->em->refresh($user); // Recharger l’entité depuis la BDD
                $this->addFlash('error', 'Le mot de passe est incorrect. Aucune modification effectuée.');
            }
        }

        return $this->render('user/index.html.twig', [
            'form' => $form,
            "title" => "Utilisateur",
        ]);
    }
}
