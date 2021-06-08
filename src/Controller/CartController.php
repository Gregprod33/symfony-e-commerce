<?php

namespace App\Controller;

use App\Cart\CartService;
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
    public function add($id, ProductRepository $productRepository, CartService $cartService): Response
    {

        $product = $productRepository->find($id);

        if (!$product) {

            throw $this->createNotFoundException("le produit $id n'existe pas !");
        }

        $cartService->add($id);
       
        $this->addFlash('success', "Le produit a bien été ajouté au panier !");


        return $this->redirectToRoute('product_show', [
            'category_slug' => $product->getCategory()->getSlug(),
            'slug' => $product->getSlug()
        ]);
    }


    /**
     * @Route("/cart/show", name="cart_show")
     */
    public function show(CartService $cartService)
    {

        $detailedCart = $cartService->getDetailedCartItem();
        

        $total = $cartService->getTotal();

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
