<?php

namespace App\Controller\Purchase;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PurchasePaymentController extends AbstractController
{

    /**
     * @Route("/purchase/payment/{id}", name="purchase_payment_form")
     */
    public function showPaymentForm() {

        return $this->render('purchase/payment.html.twig');
    }

}