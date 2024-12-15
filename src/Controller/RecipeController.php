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
        $recipe = new Recipe();
        $form = $this->createForm(RecipeType::class, $recipe);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($recipe);
            $entityManager->flush();
            return $this->redirectToRoute('app_home');
        }
        return $this->render('recipe/index.html.twig', [
            'form' => $form,
        ]);
    }
    #[Route('/recipe/show/{uuid}', name: 'recipe_show')]
    public function show(#[MapEntity(mapping: ['uuid' => 'uuid'])] Recipe $recipe, Request $request, EntityManagerInterface $entityManager): Response
    {

        return $this->render('recipe/show.html.twig', [
            'recipe' => $recipe,
        ]);
    }
}
