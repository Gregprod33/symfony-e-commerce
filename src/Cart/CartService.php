<?php

namespace App\Cart;

use App\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class CartService
{

    protected $session;
    protected $productRepository;

    public function __construct(SessionInterface $session, ProductRepository $productRepository)
    {
        $this->session = $session;
        $this->productRepository = $productRepository;
    }

    protected function getCart(): array
    {
        return $this->session->get('cart', []);
    }

    protected function saveCart(array $cart)
    {
        $this->session->set('cart', $cart);
    }



    public function add(int $id)
    {
        // Je viens chercher un tableau 'cart' dans ma session, s'il n'existe pas encore, je lui affecte un tableau vide !
        $cart = $this->getCart();
        if (!array_key_exists($id, $cart)) {
            $cart[$id] = 0; //si le produit n'existe pas, sa quantité est alors de 0
        }

        $cart[$id]++; //Sinon je l'incrémente.

        $this->saveCart($cart); //Je fixe mon tableau 'cart' avec les valeurs mises à jour !
    }



    public function getDetailedCartItem(): array
    {

        $detailedCart = [];

        foreach ($this->getCart() as $id => $qty) {
            $product = $this->productRepository->find($id);
            dump($product);

            if (!$product) {
                continue;
            }

            $detailedCart[] = new CartItem($product, $qty);
        }
        return $detailedCart;
    }


    public function getSuperTotal()
    {
        $total = 0;

        foreach ($this->getCart() as $id => $qty) {
            $product = $this->productRepository->find($id);

            if (!$product) {
                continue;
            }

            $total += $product->getPrice() * $qty;
        }

        return $total;
    }


    public function renderCartQty()
    {
        $items = $this->getCart();
        $totalQty = 0;

        if (!$items) {
            return $totalQty = 0;
        } else {
            $totalQty = array_sum($items);
            return $totalQty;
        }
    }


    public function remove(int $id)
    {
        $cart = $this->getCart();

        unset($cart[$id]);

        $this->saveCart($cart);
    }



    public function decrement(int $id)
    {
        $cart = $this->getCart();

        //je récupère mon panier, s'il n'ya pas de produits, je ne fais rien.
        if (!array_key_exists($id, $cart)) {
            return;
        }

        //Si la quantité du produit est égale à 1, je le supprime carrément.
        if ($cart[$id] === 1) {
            $this->remove($id);
            return;
        }

        //Sinon, je décrémente sa quantité et je fixe le panier.
        $cart[$id]--;


        $this->saveCart($cart);
    }
}
