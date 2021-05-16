<?php

namespace App\Controller;

use App\Taxes\Detector;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;


class HelloController extends AbstractController
{
    protected $detector;

    public function __construct(Detector $detector)
    {
        $this->detector = $detector;

    }

    /** *
     *  @Route("/hello/{prenom?world}", name="hello", methods={"GET", "POST"}), host="localhost", schemes={"http", "https"})
     */
    public function hello($prenom = "world")
    {

        dump($this->detector->detect(50));
        dump($this->detector->detect(105));
        return $this->render('hello.html.twig', [
            'prenom' => $prenom
        ]);
    }

    /**
     * @Route("/example", name ="example")
     */
    public function example()
    {

        return $this->render('example.html.twig', [
            'age' => 33
        ]);
    }
}
