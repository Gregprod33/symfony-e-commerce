<?php

namespace App\Controller;

use App\Taxes\Calculator;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TestController
{

    // protected $calculator;

    // public function __construct(Calculator $calculator)
    // {
    //     $this->calculator = $calculator;
    // }
    /**
     * @Route("/index", name="index")      
     */
    public function index(Calculator $calculator)
    {
        $tva = $calculator->calcul(80);
        dump($tva);
        dd("ça fonctionne");
    }

    /**
     * @Route("/test/{age<\d+>?0}", name="test", methods={"GET", "POST"}), host="localhost", schemes={"http", "https"})      
     */
    public function test($age)
    {
        // $request = Request::createFromGlobals(); //provient du package http foundation et permet d'analyser la requete http

        // crée un objet de la classe Request avec la méthode statique createFromGlobals qui gère l'ensemble des superglobales.

        // $age = $request->query->get('age', 0); 
        //grace à la propriété query on récupère la superglobale GET passé à l'url, si elle n'existe pas, on affecte la valeur 0
        // les propriétes comme query ou encore request présents dans request sont des bags ou tableaux associatifs

        // $age = $request->attributes->get('age', 0);

        return new Response("vous avez $age ans");
        //permet au controlleur de renvoyer une réponse
        //On doit pour cela creer une instance de la classe Response (toujours dans http foundation)



    }
}
