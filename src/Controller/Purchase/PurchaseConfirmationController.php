<?php

namespace App\Controller\Purchase;

use App\Cart\CartService;
use App\Entity\Purchase;
use App\Entity\PurchaseItem;
use App\Form\CartConfirmationType;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class PurchaseConfirmationController extends AbstractController
{

    /**
     * @Route("/purchase/confirm", name="purchase_confirm")
     */
    public function confirm(Request $request, CartService $cartService, EntityManagerInterface $em)
    {

        $form = $this->createForm(CartConfirmationType::class);
        $form->handleRequest($request);

        if (!$form->isSubmitted()) {

            $this->addFlash('warning', 'Vous devez soumettre le formulaire');
            return $this->redirectToRoute('cart_show');
        }

        $user = $this->getUser();

        if (!$user) {
            throw new AccessDeniedException("Vous devez vous connecter pour confirmer une commande");
        }

        $cartItems = $cartService->getDetailedCartItems();

        if (count($cartItems) === 0) {
            $this->addFlash('warning', 'votre panier est vide');

            return $this->redirectToRoute('cart_show');
        }

        /**@var Purchase */
        $purchase = $form->getData(); //J'ai indiqué au formulaire qu'il devait rendre les donnees sous la forme de l'entite purchase
        date_default_timezone_set('Europe/Paris');
        $date = new DateTime();
        $purchase->setUser($user); // J'ajoute juste l'utilisateur
        $purchase->setPurchasedAt($date); // Et la date de commande
        $purchase->setTotal($cartService->getSuperTotal());

        $em->persist($purchase);

        foreach ($cartService->getDetailedCartItems() as $cartItem){   // Pour chaque produit du panier, je crée une ligne pour la commande;   
            $purchaseItem = new PurchaseItem;
            $purchaseItem->setPurchase($purchase)
            ->setProduct($cartItem->product)
            ->setProductName($cartItem->product->getPrice())
            ->setQuantity($cartItem->qty)
            ->setTotal($cartItem->getTotalPricePerItem())
            ->setProductPrice($cartItem->product->getPrice());
        
        $em->persist($purchaseItem);

        }

        $em->flush();
        $this->addFlash('success', "La commande a été enregistrée");
        return $this->redirectToRoute('purchase_index');
    }
}
