<?php

namespace App\Form;

use App\Entity\Category;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CategorieType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Nom', 
                # Even 1 attribute is in an array
                'attr' => [
                    'placeholder' => 'Nom de la catégorie',
                ],
                'required' => true, # de base à true
            ])
            ->add('enable', CheckboxType::class, [
                'label' => 'Actif',
                'required' => false,
            ])

        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            # Entity : type de Classe des models qui travaillent avec l'ORM
            # Classe::class = appel au namespace de la classe
            'data_class' => Category::class,
        ]);
    }
}
