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

#[IsGranted('ROLE_ADMIN')]
final class UserController extends AbstractController
{

    public function __construct(private EntityManagerInterface $em) {}

    #[Route('/admin', name: 'app_admin', methods: ['GET', 'POST'])]
    public function index(Request $request, UserPasswordHasherInterface $ph): Response
    {
        $user = $this->getUser();
        $form = $this->createForm(UserForm::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $form->getData();
            $plainPassword = $form->get('password')->getData();

            if ($plainPassword) {
                $user->setPassword($ph->hashPassword($user, $plainPassword));
            }
            $this->em->flush();

            return $this->redirectToRoute('app_admin');
        }

        return $this->render('user/index.html.twig', [
            "form" => $form,
            "title" => "Administrateur",
        ]);
    }
}
