<?php

namespace App\Form;

use App\Entity\Task;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class TaskType extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {

        $builder 
        ->add('name', TextType::class, [
            'label' => 'Nom',
        ])

        ->add('category', null, [
            'label' => 'Catégorie',
            'choice_label' => 'getNameCategory',
        ])

        ->add('descritpionTask', TextType::class, [
            'label' => 'Description',
        ])
        ->add('priority', ChoiceType::class, [
            'label' => 'Priorité',
            'choices' => [
                0 => 0,
                1 => 1,
                2 => 2,
                3 => 3,
            ],

            

        ]);

        
        
     }
 
    /**
     * Les options du form ou "de la balise form"
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Task::class,
            // Nos attributs HTML
            'attr' => [
                'novalidate' => 'novalidate',
            ]
        ]);
    }


}
