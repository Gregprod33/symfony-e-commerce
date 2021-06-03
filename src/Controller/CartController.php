<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class CartController extends AbstractController
{
    /**
     * @Route("/cart/add/{id}", name="cart_add", requirements={"id":"\d+"})
     */
    public function add($id, ProductRepository $productRepository, SessionInterface $session, FlashBagInterface $flashBag): Response
    {
        // 0. Securisation: est-ce que le produit existe ?

        $product = $productRepository->find($id);

        if (!$product) {

            throw $this->createNotFoundException("le produit $id n'existe pas !");
        }

        // 1. Retrouver le panier dans la session sous forme de tableau clé(id du produit) => valeur(quantité du produit)


        // 2. S'il n'existe pas encore, alors prendre un tableau vide


        $cart = $session->get('cart', []);   //je recherche le tableau cart, si pas de cart, je crée un tableau vide

        // Ex tableau : [12 => 4, 29 => 2]

        // 3. Voir si le produit ($id) existe déjà dans le tableau 
        // 4. S'il c'est le cas, augmanter la quantité
        // 5. Sinon, ajouter le produit à la quantité 1

        if (array_key_exists($id, $cart)) {
            $cart[$id]++;
        } else {
            $cart[$id] = 1;
        }
        // 6. Enregistrer le tableau mis à jour dans la session

        $session->set('cart', $cart);
        // dd($session->get('cart'));

        $this->addFlash('success', "Le produit a bien été ajouté au panier !");


        return $this->redirectToRoute('product_show', [
            'category_slug' => $product->getCategory()->getSlug(),
            'slug' => $product->getSlug()
        ]);
    }


    /**
     * @Route("/cart/show", name="cart_show")
     */
    public function show(SessionInterface $session, ProductRepository $productRepository)
    {
        $detailedCart = [];
        $total = 0;

        foreach ($session->get('cart') as $id => $qty) {
            $product = $productRepository->find($id);

            $detailedCart[] = [
                'product' => $product,
                'qty' => $qty,
            ];

            $total += ($product->getPrice() * $qty);
        }



        return $this->render('cart/index.html.twig', [
            'items' => $detailedCart,
            'total' => $total
        ]);
    }



    public function renderCartQty(SessionInterface $session)
    {
        $items = $session->get('cart');
        $totalQty = 0;
      
       

        if ($items != null) {
            foreach ($items as $id => $qty) {
                $totalQty += $items[$id];
                
                return $this->render('shared/_cartQuantity.html.twig', [
                    'quantity' => $totalQty
                ]);
            }
        } else {
            return $this->render('shared/_cartQuantity.html.twig', [
                'quantity' => 0
            ]);
        }
    }
}
