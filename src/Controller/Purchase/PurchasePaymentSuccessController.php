<?php

namespace App\Controller\Purchase;

use App\Entity\Purchase;
use App\Cart\CartService;
use App\Repository\PurchaseRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PurchasePaymentSuccessController extends AbstractController
{

    protected $purchaseRepository;
    protected $em;
    protected $cartService;

    public function __construct(PurchaseRepository $purchaseRepository, EntityManagerInterface $em, CartService $cartService) {
        $this->purchaseRepository = $purchaseRepository;
        $this->em = $em;
        $this->cartService = $cartService;
    }


    /**
     * @Route("/purchase/finish/{id}", name="purchase_payment_success")
     * @IsGranted("ROLE_USER")
     */
    public function success($id) {
        //1. Je récupère la commande

        $purchase = $this->purchaseRepository->find($id);


        if(!$purchase ||
        ($purchase && $purchase->getStatus() === Purchase::STATUS_PAID) ||
        ($purchase && $purchase->getUser() !== $this->getUser())) {

            $this->addFlash('warning', 'Cette commande n\'existe pas');
            return $this->redirectToRoute('purchase_index');
        }


        //2. La commande prend le status PAID
        $purchase->setStatus(Purchase::STATUS_PAID);
        $this->em->flush();

        //3. Vider le panier
        $this->cartService->empty();
        //4. Redirection vers la liste des commandes

        $this->addFlash('success', 'La commande est bien confirmée');

        return $this->redirectToRoute('purchase_index');
    }

}


