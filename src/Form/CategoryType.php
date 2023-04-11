<?php

namespace App\Form;

use App\Entity\Category;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class CategoryType extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder 
        ->add('name_category', TextType::class, [
            'label' => 'Nom',
            'required' => true,
        ])

        ->add('project', null, [
            'label' => 'Projet',
            'choice_label' => 'getTitle',
        ]);
        
        
       
    }
 
    /**
     * Les options du form ou "de la balise form"
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Category::class,
            // Nos attributs HTML
            'attr' => [
                'novalidate' => 'novalidate',
            ]
        ]);
    }


}