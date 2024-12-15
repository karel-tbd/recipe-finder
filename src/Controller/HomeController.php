<?php

namespace App\Controller;

use App\Form\SearchFormType;
use App\Repository\FoodGroupRepository;
use App\Repository\RecipeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SearchType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{


    #[Route('/home', name: 'app_home')]
    public function index(FoodGroupRepository $foodGroupRepository, RecipeRepository $recipeRepository, Request $request): Response
    {
        $session = $request->getSession();
        if (!empty($search = $request->query->all('search'))) {
            $session->set('ingredient_search', $search);
        } else {
            $search = $session->get('ingredient_search', []);
        }
        $foodGroups = $foodGroupRepository->findAll();
        $form = $this->createForm(SearchFormType::class, null, ['search' => $search]);
        $form->handleRequest($request);
        $recipes = $recipeRepository->search($search);

        return $this->render('home/index.html.twig',[
            'foodGroups' => $foodGroups,
            'form' => $form,
            'recipes' => $recipes,
        ]);
    }
}
