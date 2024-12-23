<?php

namespace App\Controller;

use App\Entity\Recipe;
use App\Entity\RecipeIngredients;
use App\Entity\UserRecipeRating;
use App\Entity\UserRecipeSaved;
use App\Form\RecipeType;
use App\Repository\RecipeRepository;
use App\Repository\UserRecipeRatingRepository;
use App\Repository\UserRecipeSavedRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class RecipeController extends AbstractController
{

    #[Route('/recipes', name: 'recipe_index', methods: ['GET'])]
    public function index(Request $request, RecipeRepository $recipeRepository): Response
    {
        $limit = 21;
        $page = $request->query->getInt('page', 1);
        $recipes = $recipeRepository->findBy([], null, $limit, ($page - 1) * $limit);

        if ($request->query->has('page')) {
            return new JsonResponse($this->renderView('recipe/recipe_items.html.twig', [
                'recipes' => $recipes,
            ]));
        }
        if (empty($recipes)) {
            return new JsonResponse('', 200);
        }
        return $this->render('recipe/index.html.twig', [
            'recipes' => $recipes,
            'current_page' => $page,
        ]);
    }

    #[Route('/recipe/add', name: 'recipe_add')]
    public function add(Request $request, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(RecipeType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $recipe = $form->getData();
            foreach ($form->get('ingredients')->getData() as $data) {
                $recipeIngredient = new RecipeIngredients();
                $recipeIngredient->setRecipe($recipe);
                $recipeIngredient->setIngredient($data['ingredient']);
                $recipeIngredient->setQuantity($data['quantity']);
                $recipeIngredient->setUnit($data['unit']);
                $entityManager->persist($recipeIngredient);
            }
            $entityManager->persist($recipe);
            $entityManager->flush();
            return $this->redirectToRoute('recipe_index');
        }
        return $this->render('recipe/add.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/recipe/show/{uuid}', name: 'recipe_show')]
    public function show(#[MapEntity(mapping: ['uuid' => 'uuid'])] Recipe $recipe, Request $request, EntityManagerInterface $entityManager, UserRecipeRatingRepository $userRecipeRatingRepository, UserRecipeSavedRepository $userRecipeSavedRepository): Response
    {
        $session = $request->getSession();
        $searchIngredients = null;
        if (isset($session->get('ingredient_search')['search'])) {
            $searchIngredients = $session->get('ingredient_search')['search'];
        }
        $recipeSavedByUser = $userRecipeSavedRepository->findOneBy(['recipe' => $recipe, 'user' => $this->getUser()]);
        $score = $userRecipeRatingRepository->findOneBy(['recipe' => $recipe, 'user' => $this->getUser()]);
        if (!empty($score)) {
            $score = $score->getScore();
        }
        return $this->render('recipe/show.html.twig', [
            'recipe' => $recipe,
            'searchIngredients' => $searchIngredients,
            'recipeSavedByUser' => $recipeSavedByUser,
            'score' => $score,
        ]);
    }

    #[Route('/recipe/edit/{uuid}', name: 'recipe_edit')]
    public function edit(#[MapEntity(mapping: ['uuid' => 'uuid'])] Recipe $recipe, Request $request, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(RecipeType::class, $recipe, ['edit' => true]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($recipe);
            $entityManager->flush();
            return $this->redirectToRoute('recipe_index');
        }
        return $this->render('recipe/edit.html.twig', [
            'form' => $form,
            'recipe' => $recipe,
        ]);
    }

    #[Route('/recipe/save/{uuid}', name: 'recipe_save')]
    public function save(#[MapEntity(mapping: ['uuid' => 'uuid'])] Recipe $recipe, Request $request, EntityManagerInterface $entityManager, UserRecipeSavedRepository $userRecipeSavedRepository, Security $security): Response
    {
        if (!$userRecipeSaved = $userRecipeSavedRepository->findOneBy(['recipe' => $recipe, 'user' => $security->getUser()])) {
            $userRecipeSaved = new UserRecipeSaved();
            $userRecipeSaved->setRecipe($recipe);
            $userRecipeSaved->setUser($security->getUser());
            $entityManager->persist($userRecipeSaved);
            $this->addFlash('success', 'Recipe saved!');
        } else {
            $entityManager->remove($userRecipeSaved);
            $this->addFlash('error', 'Recipe removed from saved!');
        }
        $entityManager->flush();
        return $this->redirectToRoute('recipe_show', ['uuid' => $recipe->getUuid()]);
    }

    #[Route('/my-recipes', name: 'recipe_saved')]
    public function saved(): Response
    {
        return $this->render('my-recipes/index.html.twig');
    }

    #[Route('/recipe/rating', name: 'recipe_set_rating', methods: ['POST'])]
    public function rating(RecipeRepository $recipeRepository, Security $security, EntityManagerInterface $entityManager, UserRecipeRatingRepository $userRecipeRatingRepository, Request $request): Response
    {
        $content = json_decode($request->getContent(), true);
        $score = $content['clicked'];
        $uuid = $content['uuid'];

        $recipe = $recipeRepository->findOneBy(['uuid' => $uuid]);

        if (!$rating = $userRecipeRatingRepository->findOneBy(['recipe' => $recipe, 'user' => $security->getUser()])) {
            $rating = new UserRecipeRating();
        }
        $rating->setRecipe($recipe);
        $rating->setUser($security->getUser());
        $rating->setScore($score + 1);
        $entityManager->persist($rating);
        $entityManager->flush();

        return $this->redirectToRoute('recipe_show', ['uuid' => $recipe->getUuid()]);
    }

    #[Route('/recipe/find', name: 'recipe_find', methods: ['POST'])]
    public function find(Request $request, RecipeRepository $recipeRepository): JsonResponse
    {
        $content = json_decode($request->getContent(), true);
        $mealType = $content['mealType'];
        $country = $content['country'];
        $difficulty = $content['difficulty'];

        $recipes = $recipeRepository->findMeal($mealType, $country, $difficulty);
        $session = $request->getSession();
        $session->set('recipes', $recipes);
        return new JsonResponse([
            'redirectUrl' => $this->generateUrl('recipe_found'),
        ]);
    }

    #[Route('/recipe/found', name: 'recipe_found')]
    public function found(Request $request, RecipeRepository $recipeRepository): Response
    {
        $session = $request->getSession();
        $recipes = $session->get('recipes');
        return $this->render('recipe/found.html.twig', [
            'recipes' => $recipes,
        ]);
    }
}

