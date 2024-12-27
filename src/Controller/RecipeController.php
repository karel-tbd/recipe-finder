<?php

namespace App\Controller;

use App\Entity\General\Preregistration;
use App\Entity\Recipe;
use App\Entity\RecipeIngredients;
use App\Entity\UserRecipeRating;
use App\Entity\UserRecipeSaved;
use App\Enum\Publish;
use App\Form\RecipeType;
use App\Repository\RecipeIngredientsRepository;
use App\Repository\RecipeRepository;
use App\Repository\UserRecipeRatingRepository;
use App\Repository\UserRecipeSavedRepository;
use Doctrine\ORM\EntityManagerInterface;
use Pontedilana\PhpWeasyPrint\Pdf;
use Pontedilana\WeasyprintBundle\WeasyPrint\Response\PdfResponse;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Routing\Attribute\Route;
use Twig\Environment;

class RecipeController extends AbstractController
{

    #[Route('/recipes', name: 'recipe_index', methods: ['GET'])]
    public function index(): Response
    {
        return $this->render('recipe/index.html.twig');
    }

    #[Route('/recipe/add', name: 'recipe_add')]
    public function add(Request $request, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(RecipeType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $publish = $form->get('publish')->getData();
            $recipe = $form->getData();
            foreach ($form->get('ingredients')->getData() as $data) {
                $recipeIngredient = new RecipeIngredients();
                $recipeIngredient->setRecipe($recipe);
                $recipeIngredient->setIngredient($data['ingredient']);
                $recipeIngredient->setQuantity($data['quantity']);
                $recipeIngredient->setUnit($data['unit']);
                $entityManager->persist($recipeIngredient);
            }
            if ($publish) {
                $recipe->setPublish(Publish::PENDING);
            } else {
                $recipe->setPublish(Publish::PRIVATE);
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
    public function edit(#[MapEntity(mapping: ['uuid' => 'uuid'])] Recipe $recipe, Request $request, EntityManagerInterface $entityManager, RecipeIngredientsRepository $recipeIngredientsRepository): Response
    {
        $recipeIngerdients = $recipeIngredientsRepository->findBy(['recipe' => $recipe]);
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
            'recipeIngredients' => $recipeIngerdients,
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
        $meal = $content['meal'];
        $country = $content['country'];
        $difficulty = $content['difficulty'];
        $mealType = $content['mealType'];

        $recipes = $recipeRepository->findMeal($meal, $country, $difficulty, $mealType);
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

    #[Route('/recipe/manage', name: 'recipe_manage')]
    public function manage(RecipeRepository $recipeRepository): Response
    {
        $recipes = $recipeRepository->findBy(['status' => Publish::PENDING]);
        return $this->render('recipe/manage.html.twig', [
            'recipes' => $recipes,
        ]);
    }

    #[Route('/recipe/manage/reject/{uuid}', name: 'recipe_manage_reject')]
    public function reject(#[MapEntity(mapping: ['uuid' => 'uuid'])] Recipe $recipe, EntityManagerInterface $entityManager, RecipeRepository $recipeRepository): Response
    {
        $entityManager->remove($recipe);
        $entityManager->flush();

        $recipes = $recipeRepository->findBy(['status' => Publish::PENDING]);
        return $this->render('recipe/manage.html.twig', [
            'recipes' => $recipes,
        ]);
    }

    #[Route('/recipe/manage/accept/{uuid}', name: 'recipe_manage_accept')]
    public function accept(#[MapEntity(mapping: ['uuid' => 'uuid'])] Recipe $recipe, EntityManagerInterface $entityManager, RecipeRepository $recipeRepository): Response
    {
        $recipe->setStatus(Publish::PUBLISHED);
        $entityManager->flush();

        $recipes = $recipeRepository->findBy(['status' => Publish::PENDING]);
        return $this->render('recipe/manage.html.twig', [
            'recipes' => $recipes,
        ]);
    }

    #[Route('/recipe/pdf/{uuid}', name: 'recipe_pdf'), ]
    public function pdf(#[MapEntity(mapping: ['uuid' => 'uuid'])] Recipe $recipe, Environment $twig, Pdf $weasyPrint): PdfResponse
    {
        $html = $twig->render('recipe/pdf.html.twig', [
                'recipe' => $recipe,
            ]
        );
        $pdfContent = $weasyPrint->getOutputFromHtml($html);

        return new PdfResponse(
            content: $pdfContent,
            fileName: 'recipe.pdf',
            contentType: 'application/pdf',
            contentDisposition: ResponseHeaderBag::DISPOSITION_INLINE,
            status: 200,
            headers: []
        );
    }

    #[Route('/recipe/delete/{uuid}', name: 'recipe_delete')]
    public function delete(#[MapEntity(mapping: ['uuid' => 'uuid'])] Recipe $recipe, EntityManagerInterface $entityManager, Security $security): Response
    {
        if ($recipe->getCreatedBy() === $security->getUser() or $this->isGranted('ROLE_ADMIN')) {
            foreach ($recipe->getRecipeIngredients() as $ingredient) {
                $entityManager->remove($ingredient);
            }
            $entityManager->remove($recipe);
            $entityManager->flush();
            return $this->redirectToRoute('recipe_index');
        }
        return $this->redirectToRoute('recipe_index');
    }
}

