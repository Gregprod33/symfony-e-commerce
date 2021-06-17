<?php

namespace App\Controller\Purchase;

use App\Cart\CartService;
use App\Entity\User;
use App\Repository\PurchaseRepository;
use App\Repository\UserRepository;
use Twig\Environment;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;


class PurchasesListController extends AbstractController
{

    /**
     * @Route("/purchases", name="purchase_index")
     * @IsGranted("ROLE_USER", message="Vous devez être un utilisateur pour accéder aux commandes")
     */
    public function index(PurchaseRepository $purchaseRepository)
    {

        //1. S'assurer que la personne est connectée sinon redirection vers la page d'accueil

        /**@var User */
        $user = $this->getUser();

        //afichage des commandes les plus récentes en premier
        $purchases = $purchaseRepository->findBy(
            ['user' => $user],
            ['purchasedAt' => 'desc']
        );



        //2. Savoir qui est connectée

        //3. Passer le user a twig
        return $this->render('purchase/index.html.twig', [
            'purchases' => $purchases

        ]);
    }
}
