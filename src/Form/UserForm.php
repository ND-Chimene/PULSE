<?php

namespace App\Form;

use Dom\Text;
use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

class UserForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, [
                'row_attr' => ['class' => 'w-full flex flex-col gap-2'],
                'label' => 'Votre adresse e-mail',
                'attr' => ['class' => 'form-control text-primary-black rounded-lg px-5 py-2'],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez entrer une adresse email'
                    ]),
                    new Email([
                        'message' => 'L\'adresse email {{ value }} n\'est pas valide'
                    ])
                ]
            ])
            ->add('firstname', TextType::class, [
                'row_attr' => ['class' => 'w-full flex flex-col gap-2'],
                'label' => 'Votre prénom',
                'attr' => ['class' => 'form-control text-primary-black rounded-lg px-5 py-2'],
                'required' => true,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez entrer votre prénom'
                    ]),
                    new Length([
                        'min' => 2,
                        'max' => 50,
                        'minMessage' => 'Votre prénom doit contenir au moins {{ limit }} caractères',
                        'maxMessage' => 'Votre prénom ne peut pas dépasser {{ limit }} caractères'
                    ])
                ]
            ])
            ->add('lastname', TextType::class, [
                'row_attr' => ['class' => 'w-full flex flex-col gap-2'],
                'label' => 'Votre nom',
                'attr' => ['class' => 'form-control text-primary-black rounded-lg px-5 py-2'],
                'required' => true,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez entrer votre nom'
                    ]),
                    new Length([
                        'min' => 2,
                        'max' => 50,
                        'minMessage' => 'Votre nom doit contenir au moins {{ limit }} caractères',
                        'maxMessage' => 'Votre nom ne peut pas dépasser {{ limit }} caractères'
                    ])
                ]
            ])
            ->add('password', PasswordType::class, [
                'mapped' => false,
                'row_attr' => ['class' => 'w-full flex flex-row justify-between gap-10'],
                'label' => 'Saisisez votre mot de passe pour mettre à jour votre profil',
                'label_attr' => ['class' => 'form-label text-primary-black rounded-lg px-5 py-2'],
                'attr' => [
                    'placeholder' => 'Mot de passe',
                    'class' => 'form-control',
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez entrer un mot de passe'
                    ]),
                    new Length([
                        'min' => 12,
                        'minMessage' => 'Votre mot de passe doit faire au moins {{ limit }} caractères',
                        'max' => 4096,
                    ])
                ]
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Enregistrer les modifications',
                'attr' => ['class' => 'w-full text-primary-white bg-primary-black rounded-lg px-5 py-2 flex items-center justify-center gap-2 hover:bg-secondary-black transition-all duration-300'],
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
