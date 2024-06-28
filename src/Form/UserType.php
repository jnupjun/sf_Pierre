<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;

use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('firstName', TextType::class, [
                'label' => 'Prénom',
                'attr' => [
                    'placeholder' => 'John',
                ],
            ])
            ->add('lastName', TextType::class, [
                'label' => 'Nom',
                'attr' => [
                    'placeholder' => 'Doe',
                ],
            ])
            ->add('email', EmailType::class, [
                'label' => 'Email',
                'attr' => [
                    'placeholder' => 'john@exemple.com',
                ]
            ])
            # ->add('roles') # we don't want him to attributes him a role
            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'invalid_message' => 'The password fields must match.',
                'first_options' => [
                    'label' => 'Mot de passe',
                    'attr' => [
                        'placeholder' => 'secret',
                    ],
                    'constraints' => [
                        new NotBlank(),
                        new Length([
                            'max' => 4096,
                            # Possible faille de sécurit
                        ]),
                        new Regex(
                            pattern: '/^(?=.*\d)(?=.*[A-Z])(?=.*[a-z])(?=.*[^\w\d\s:])([^\s]){8,16}$/',
                            message: 'Le mot de passe doit contenir 1 majuscule, 1 minuscule, 1 chiffre et 1 caractère spécial. Longueur entre 8 et 16 caractères.',
                        )
                    ]
                ],
                'second_options' => [
                    'label' => 'Confirmer le mot de passe',
                    'attr' => [
                        'placeholder' => 'secret',
                    ]
                ],
                # Break automation of the input to hash it, for the password
                # This is the sole reason to put all constraints here 
                # and not in the entity like the other fields !! (like email, first/lastNames)
                'mapped' => false,
            ]);

        # Of course is Admin !!!
        if ($options['isAdmin']) {
            $builder
                ->remove('password')
                # https://symfony.com/doc/current/reference/forms/types/choice.html#example-usage
                ->add('roles', ChoiceType::class, [
                    'label' => 'Rôle',
                    'choices' => [
                        'Utilisateur' => 'ROLE_USER',
                        'Éditeur' => 'ROLE_EDITOR',
                        'Admin' => 'ROLE_ADMIN',
                    ],
                    'expanded' => true,
                    'multiple' => true,
                ]);
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            # ajout de cette option pour le admin/users/id/update
            'isAdmin' => false,
        ]);
    }
}
