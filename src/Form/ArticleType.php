<?php

namespace App\Form;

use App\Entity\Article;
use App\Entity\Category;
use App\Repository\CategoryRepository;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class ArticleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'Titre',
                'attr' => [
                    'placeholder' => 'Titre de l\'article',
                ],
            ])
            ->add('categories', EntityType::class, [
                'class' => Category::class,
                'choice_label' => 'name', # we use this attribute to display in the twig template, 
                'label' => 'Catégories',
                'expanded' => false,
                'multiple' => true, # ces 2 entrées dans le tableau permettent la liste déroulante
                # https://symfony.com/doc/current/reference/forms/types/entity.html
                # by reference : false before it was mandatory
                'autocomplete' => true,
                # as shown here : https://symfony.com/bundles/ux-autocomplete/current/index.html#usage-in-a-form-without-ajax
                'query_builder' => function (CategoryRepository $repo): QueryBuilder {
                    return $repo->createQueryBuilder('c')
                        ->andWhere('c.enable = true')
                        ->orderBy('c.name', 'ASC');
                },
                # This function could be put in the CategoryRepository
                # https://symfony.com/doc/current/reference/forms/types/entity.html#using-a-custom-query-for-the-entities
            ])
            ->add('content', TextareaType::class, [
                'label' => 'Article',
                'attr' => [
                    'placeholder' => 'Contenu de l\'article',
                    'rows' => 10,
                ],
            ])

            ->add('enable', CheckboxType::class, [
                'label' => 'Actif',
                'required' => false,
                # by default, the required value of all types of inputs is set to true
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Article::class,
            'isAdmin' => false,
        ]);
    }
}
