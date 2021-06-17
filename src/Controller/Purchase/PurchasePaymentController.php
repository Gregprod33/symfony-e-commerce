<?php

namespace App\Controller\Purchase;

use App\Repository\PurchaseRepository;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PurchasePaymentController extends AbstractController
{

    /**
     * @Route("/purchase/payment/{id}", name="purchase_payment_form")
     */
    public function showPaymentForm($id, PurchaseRepository $purchaseRepository)
    {

        $purchase = $purchaseRepository->find($id);
        $total = $purchase->getTotal() * 100;

        if (!$purchase) {
            return $this->redirectToRoute('cart_Show');
        }
        \Stripe\Stripe::setApiKey('sk_test_51J1yCfEGMtU5V1DR30nUE9kcWVVrpZxfGKhhW9oNXu14zJjHV6ZEGQCSbLWNetiE60xW525r9tz3I90t7X0JtfTN00Bhny10vb');

       $intent =  \Stripe\PaymentIntent::create([
            'amount' => $total,
            'currency' => 'eur',
        ]);

        

        return $this->render('purchase/payment.html.twig', [
            'clientSecret' => $intent->client_secret
        ]);
    }
}
