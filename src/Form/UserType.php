<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;

class UserType extends AbstractType { 

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nickname', TextType::class, [
                'label' => 'Pseudo',
            ])

            
            ->add('email')

            ->add('password', PasswordType::class, [
                'label' => 'Mot de passe',

            ])

            ->addEventListener(FormEvents::PRE_SET_DATA, function(FormEvent $event) {
                // We get the form from the event
                $form = $event->getForm();
                // We get the user mapped to the form from the event
                $user = $event->getData();

             

        
                // If user exists, it has id non null
                if ($user->getId() !== null) {
                    // Edit
                    $form->add('password', null, [
                        // Pour le form d'édition, on n'associe pas le password à l'entité
                        // @link https://symfony.com/doc/current/reference/forms/types/form.html#mapped
                        'mapped' => false,
                        'attr' => [
                            'placeholder' => 'Laissez vide si inchangé'
                        ]
                    ]);
                } else {
                    // New
                    $form->add('password', null, [
                        // In case of an error of type
                        // Expected argument of type "string", "null" given at property path "password".
                        'empty_data' => '',
                        // We move the constraints from the entity to the add-on form.
                        'constraints' => [
                            new NotBlank(),
                            new Regex(
                                "/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/",
                                "Le mot de passe doit contenir au minimum 8 caractères, une majuscule, un chiffre et un caractère spécial"
                            ),
                        ],
                    ]);
                }
            })

            ->add('picture', UrlType::class, [
                'label' => 'Photo de profil',
                'help' => 'URL de l\'image'
            ])
            -> add('roles', ChoiceType::class, [
                'label' => 'Rôles',
                'choices' => [
                    'Utilisateur' => 'ROLE_USER',
                    'Administrateur' => 'ROLE_ADMIN',
                ],

                'multiple' => true,
                'expanded' => true,

            ]);


        }

        public function configureOptions(OptionsResolver $resolver): void
        {
            $resolver->setDefaults([
                'data_class' => User::class,
                'attr' => ['novalidate' => 'novalidate']
            ]);
        }
}


    

