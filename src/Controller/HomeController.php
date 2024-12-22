<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{


    #[Route('/', name: 'app_home', methods: ['GET', 'POST'])]
    public function index(Request $request, bool $modal = false): Response
    {
        $recipes = [];
        return $this->render('home/index.html.twig', [
            'modal' => $modal,
            'recipes' => $recipes,
        ]);
    }
}
