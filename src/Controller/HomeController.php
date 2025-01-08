<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{


    #[Route('/find', name: 'app_home', methods: ['GET', 'POST'])]
    public function index(): Response
    {
        $recipes = [];
        return $this->render('home/index.html.twig', [
            'recipes' => $recipes,
        ]);
    }
}
