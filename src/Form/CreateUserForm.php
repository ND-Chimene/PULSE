<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

class CreateUserForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('firstname', TextType::class, [
                'label' => 'Prénom',
                'attr' => ['class' => 'text-primary-black rounded-lg px-5 py-2'],
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez entrer un prénom']),
                    new Length(['min' => 2, 'max' => 50]),
                ]
            ])
            ->add('lastname', TextType::class, [
                'label' => 'Nom',
                'attr' => ['class' => 'text-primary-black rounded-lg px-5 py-2'],
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez entrer un nom']),
                    new Length(['min' => 2, 'max' => 50]),
                ]
            ])
            ->add('email', EmailType::class, [
                'label' => 'Adresse e-mail',
                'attr' => ['class' => 'text-primary-black rounded-lg px-5 py-2'],
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez entrer une adresse e-mail']),
                    new Email(['message' => 'Adresse e-mail invalide']),
                ]
            ])
            ->add('plainPassword', PasswordType::class, [
                'mapped' => false,
                'label' => 'Mot de passe',
                'attr' => ['class' => 'text-primary-black rounded-lg px-5 py-2'],
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez entrer un mot de passe']),
                    new Length([
                        'min' => 12,
                        'max' => 80,
                        'minMessage' => 'Le mot de passe doit faire au moins {{ limit }} caractères',
                    ]),
                ]
            ])
            ->add('roles', ChoiceType::class, [
                'label' => 'Rôle',
                'mapped' => true,
                'expanded' => false,
                'multiple' => true,
                'attr' => ['class' => 'text-primary-black rounded-lg px-5 py-2'],
                'choices' => [
                    'Technicien IT' => 'ROLE_IT',
                    'Administrateur' => 'ROLE_ADMIN',
                ],
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Créer le compte',
                'attr' => ['class' => 'w-96 my-auto text-primary-white bg-primary-black rounded-lg px-5 py-2 flex items-center justify-center gap-2 hover:bg-secondary-black transition-all duration-300'],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
