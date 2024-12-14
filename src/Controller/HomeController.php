<?php

namespace App\Controller;

use App\Repository\FoodGroupRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{
    #[Route('/home', name: 'app_home')]
    public function index(FoodGroupRepository $foodGroupRepository): Response
    {
        $foodGroups = $foodGroupRepository->findAll();
        return $this->render('home/index.html.twig',[
            'foodGroups' => $foodGroups,
        ]);
    }
}
