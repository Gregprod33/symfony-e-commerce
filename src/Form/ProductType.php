<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\Product;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add('name', TextType::class, [
            'label' => 'Nom du produit',
            'attr' => [
                'placeholder' => 'Nom du produit'
            ]
        ])
        ->add('shortDescription', TextareaType::class, [
            'label' => 'Description courte',
            'attr' => [
                'placeholder' => 'Description du produit'
            ]

        ])
        ->add('price', MoneyType::class, [
            'label' => 'Prix',
            'attr' => [
                'placeholder' => 'Prix du produit'
            ]
        ])
        ->add('picture', UrlType::class, [
            'label' => 'Image du produit',
            'attr' => [
                'placeholder' => 'Url de l\'image'
            ]
        ])
        ->add('category', EntityType::class, [
            'label' => 'Entrez une catégorie',
            'attr' => ['class' => 'form-control'],
            'placeholder' => 'Entrez une catégorie',
            'class' => Category::class,
            'choice_label' => function (Category $category) {
                return mb_strtoupper($category->getName());
            }
        ]);
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Product::class,
        ]);
    }
}
