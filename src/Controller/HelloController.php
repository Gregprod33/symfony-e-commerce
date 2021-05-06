<?php

namespace App\Controller;

use Twig\Environment;
use App\Taxes\Calculator;
use Psr\Log\LoggerInterface;
use Cocur\Slugify\Slugify;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class HelloController
{
    protected $calculator;

    public function __construct(Calculator $calculator)
    {
        $this->calculator = $calculator;
    }
    /** *
     *  @Route("/hello/{nom}", name="hello", methods={"GET", "POST"}), host="localhost", schemes={"http", "https"})
     */
    public function hello($nom = "world", LoggerInterface $logger, Slugify $slugify, Environment $twig) 
    {

        dump($twig); 
        
        // $slugify = new Slugify();
        dump($slugify->slugify('Hello World!'));
       


        $logger->error("Mon message de log !");

        $tva = $this->calculator->calcul(100);
        dump($tva);

        $logger->error("Mon message d'erreur");
        return new Response('Hello ' . $nom);

        
        
    }
}
