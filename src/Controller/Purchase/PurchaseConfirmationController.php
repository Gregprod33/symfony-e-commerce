<?php

namespace App\Controller\Purchase;

use App\Cart\CartService;
use App\Entity\Purchase;
use App\Entity\PurchaseItem;
use App\Form\CartConfirmationType;
use App\Purchase\PurchasePersister;
use DateTime;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class PurchaseConfirmationController extends AbstractController
{

    protected $purchasePersister;
    protected $cartService;
    protected $em;

    public function __construct(PurchasePersister $purchasePersister, CartService $cartService, EntityManagerInterface $em)
    {
        $this->purchasePersister = $purchasePersister;
        $this->em = $em;
        $this->cartService = $cartService;
    }


    /**
     * @Route("/purchase/confirm", name="purchase_confirm")
     */
    public function confirm(Request $request)
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

        $cartItems = $this->cartService->getDetailedCartItems();

        if (count($cartItems) === 0) {
            $this->addFlash('warning', 'votre panier est vide');

            return $this->redirectToRoute('cart_show');
        }

        /**@var Purchase */
        $purchase = $form->getData(); //J'ai indiqué au formulaire qu'il devait rendre les donnees sous la forme de l'entite purchase

        $this->purchasePersister->storePurchase($purchase);

        
        
        return $this->redirectToRoute('purchase_payment_form', [
            'id' => $purchase->getId()
        ]);
    }
}
