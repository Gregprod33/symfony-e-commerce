<?php

namespace App\Controller;

use App\Entity\Category;
use App\Repository\CategoryRepository;
use App\Repository\ProductRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;

class ProductController extends AbstractController
{

    /**
     * @Route("/{slug}", name="product_category", priority=-1)
     */
    public function category($slug, CategoryRepository $categoryRepository)
    {
        $category = $categoryRepository->findOneBy([
            'slug' => $slug
        ]);

        if (!$category) {
            throw $this->createNotFoundException("Cette catégorie n'existe pas"); // via AbstractController
            // throw new NotFoundHttpException("Cette catégorie n'existe pas");
        }

        return $this->render('product/category.html.twig', [
            'slug' => $slug,
            'category' => $category
        ]);
    }

    /**
     * @Route("/{category_slug}/{slug}", name="product_show")
     */
    public function show($slug, ProductRepository $productRepository)
    {

        $product = $productRepository->findOneBy([
            'slug' => $slug
        ]);

        if (!$product) {
            throw $this->createNotFoundException("Ce produit n'existe pas"); // via AbstractController
            // throw new NotFoundHttpException("Cette catégorie n'existe pas");
        }

        return $this->render('product/show.html.twig', [
            'product' => $product
        ]);
    }


    /**
     * @Route("/admin/product/create", name="product_create")
     */
    public function create(FormFactoryInterface $factory, CategoryRepository $categoryRepository, Request $request)
    {
        $builder = $factory->createBuilder();

        $builder->add('name', TextType::class, [
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



            ->add('category', EntityType::class, [
                'label' => 'Entrez une catégorie',
                'attr' => ['class' => 'form-control'],
                'placeholder' => 'Entrez une catégorie',
                'class' => Category::class,
                'choice_label' => function (Category $category) {
                    return mb_strtoupper($category->getName());
                }

            ]);

        $form = $builder->getForm();

        $form->handleRequest($request);
        $data = $form->getData();
            dump($data);
        $formView = $form->createView();

        return $this->render('product/create.html.twig', [
            'formView' => $formView
        ]);
    }
}
