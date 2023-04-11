<?php 

namespace App\Controller\Back;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * 
 * home of the administrators
 * 
 * 
 */
class BackHomeController extends AbstractController {

    /**
     * 
     * @Route("/", name = "back_home", methods={"GET"})
     */

     public function index(): Response 
     {

        return $this->render('base.html.twig');
     }
}