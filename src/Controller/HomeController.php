<?php

namespace App\Controller;

use App\Form\SearchFormType;
use App\Repository\RecipeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{


    #[Route('/home', name: 'app_home')]
    public function index(RecipeRepository $recipeRepository, Request $request): Response
    {
        $dishType = [];
        $session = $request->getSession();
        if (!empty($search = $request->query->all('search'))) {
            $session->set('ingredient_search', $search);
        } else {
            $search = $session->get('ingredient_search', []);
        }

        if (!empty($filter = $request->request->all())) {
            $session->set('recipe_filter', $search);
        } else {
            $search = $session->get('recipe_filter', []);
        }

        $form = $this->createForm(SearchFormType::class, null, ['search' => $search]);
        $form->handleRequest($request);

        $recipes = $recipeRepository->search($search, $filter);


        return $this->render('home/index.html.twig', [
            'form' => $form,
            'recipes' => $recipes,
        ]);
    }
}
