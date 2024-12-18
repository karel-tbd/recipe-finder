<?php

namespace App\Controller;

use App\Entity\Recipe;
use App\Form\RecipeType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class RecipeController extends AbstractController
{
    #[Route('/recipe/add', name: 'recipe_add')]
    public function index(Request $request, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(RecipeType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $recipe = $form->getData();
            foreach ( $form->get('ingredients')->getData() as $data) {
                $recipe->addIngredient($data['ingredient']);
            }
            $entityManager->persist($recipe);
            $entityManager->flush();
            return $this->redirectToRoute('app_home');
        }
        return $this->render('recipe/add.html.twig', [
            'form' => $form,
        ]);
    }
    #[Route('/recipe/show/{uuid}', name: 'recipe_show')]
    public function show(#[MapEntity(mapping: ['uuid' => 'uuid'])] Recipe $recipe, Request $request, EntityManagerInterface $entityManager): Response
    {
        $session = $request->getSession();
        $searchIngredients = null;
        if (isset($session->get('ingredient_search')['search'])){
            $searchIngredients = $session->get('ingredient_search')['search'];
        }

        return $this->render('recipe/show.html.twig', [
            'recipe' => $recipe,
            'searchIngredients' => $searchIngredients,
        ]);
    }

    #[Route('/recipe/edit/{uuid}', name: 'recipe_edit')]
    public function edit(#[MapEntity(mapping: ['uuid' => 'uuid'])] Recipe $recipe, Request $request, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(RecipeType::class, $recipe);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($recipe);
            $entityManager->flush();
            return $this->redirectToRoute('app_home');
        }
        return $this->render('recipe/edit.html.twig', [
            'form' => $form,
            'recipe' => $recipe,
        ]);
    }

}
