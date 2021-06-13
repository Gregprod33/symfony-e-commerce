<?php

namespace App\Purchase;

use App\Cart\CartService;
use App\Entity\Purchase;
use App\Entity\PurchaseItem;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;

class PurchasePersister
{
    protected $security;
    protected $em;
    protected $cartService;

    public function __construct(Security $security, EntityManagerInterface $em, CartService $cartService){
        $this->security = $security;
        $this->em = $em;
        $this->cartService = $cartService;
    }

    public function storePurchase(Purchase $purchase){
        $user = $this->security->getUser();


        date_default_timezone_set('Europe/Paris');
        $date = new DateTime();
        $purchase->setUser($user); // J'ajoute juste l'utilisateur
        $purchase->setPurchasedAt($date); // Et la date de commande
        $purchase->setTotal($this->cartService->getSuperTotal());

        $this->em->persist($purchase);

        foreach ($this->cartService->getDetailedCartItems() as $cartItem){   // Pour chaque produit du panier, je crée une ligne pour la commande;   
            $purchaseItem = new PurchaseItem;
            $purchaseItem->setPurchase($purchase)
            ->setProduct($cartItem->product)
            ->setProductName($cartItem->product->getPrice())
            ->setQuantity($cartItem->qty)
            ->setTotal($cartItem->getTotalPricePerItem())
            ->setProductPrice($cartItem->product->getPrice());
        
        $this->em->persist($purchaseItem);

        }
        $this->em->flush();
    }
}

