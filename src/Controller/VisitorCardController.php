<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class VisitorCardController extends AbstractController
{
    /**
     * @Route("/visitor/card", name="visitor_card")
     */
    public function index(): Response
    {
        return $this->render('visitor_card/index.html.twig', [
            'controller_name' => 'VisitorCardController',
        ]);
    }
}
