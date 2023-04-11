<?php

namespace App\Form;

use App\Entity\Project;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;


class ProjectType extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {

        $builder 
        ->add('title', TextType::class, [
            'label' => 'Titre',
        ])

        ->add('descriptionProject', TextType::class, [
            'label' => 'Description',
        ])

        ->add('user', null, [
            'label' => 'Utilisateur',
            'choice_label' => 'getNickname',
        ]);

       
    }
 
    /**
     * Les options du form ou "de la balise form"
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Project::class,
            // Nos attributs HTML
            'attr' => [
                'novalidate' => 'novalidate',
            ]
        ]);
    }


}
