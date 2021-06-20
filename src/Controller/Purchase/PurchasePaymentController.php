<?php

namespace App\Controller\Purchase;

use App\Entity\Purchase;
use App\Repository\PurchaseRepository;
use App\Stripe\StripeService;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PurchasePaymentController extends AbstractController
{

    /**
     * @Route("/purchase/payment/{id}", name="purchase_payment_form")
     * @IsGranted("ROLE_USER")
     */
    public function showPaymentForm($id, PurchaseRepository $purchaseRepository, StripeService $stripeService)
    {

        $purchase = $purchaseRepository->find($id);
        $total = $purchase->getTotal() * 100;

        if(!$purchase ||
        ($purchase && $purchase->getStatus() === Purchase::STATUS_PAID) ||
        ($purchase && $purchase->getUser() !== $this->getUser())) {
            return $this->redirectToRoute('cart_Show');
        }
        $intent = $stripeService->getPaymentIntent($purchase);

       $intent =  \Stripe\PaymentIntent::create([
            'amount' => $total,
            'currency' => 'eur',
            
        ]);

        

        return $this->render('purchase/payment.html.twig', [
            'clientSecret' => $intent->client_secret,
            'purchase' => $purchase,
            'stripePublicKey' => $stripeService->getPublicKey()
        ]);
    }
}
