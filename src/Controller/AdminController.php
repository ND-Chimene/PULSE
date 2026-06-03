<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\CreateUserForm;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[IsGranted('ROLE_ADMIN')]
#[Route('/admin')]
final class AdminController extends AbstractController
{
    public function __construct(private EntityManagerInterface $em) {}

    #[Route('/users', name: 'app_admin_users', methods: ['GET'])]
    public function listUsers(UserRepository $userRepository): Response
    {
        return $this->render('admin/users.html.twig', [
            'users' => $userRepository->findBy([], ['created_at' => 'DESC']),
            'title' => 'Administration',
        ]);
    }

    #[Route('/users/new', name: 'app_admin_create_user', methods: ['GET', 'POST'])]
    public function createUser(Request $request, UserPasswordHasherInterface $ph): Response
    {
        $user = new User();
        $form = $this->createForm(CreateUserForm::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setPassword($ph->hashPassword($user, $form->get('plainPassword')->getData()));
            $this->em->persist($user);
            $this->em->flush();

            $this->addFlash('success', sprintf('Compte créé pour %s.', $user->getFullname()));
            return $this->redirectToRoute('app_admin_create_user');
        }

        return $this->render('admin/create_user.html.twig', [
            'form' => $form,
            'title' => 'Administration',
        ]);
    }
}
