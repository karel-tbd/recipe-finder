<?php

namespace App\Controller;

use App\Entity\Recipe;
use App\Entity\UserRecipeRating;
use App\Entity\UserRecipeSaved;
use App\Enum\Publish;
use App\Form\RecipeType;
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
    #[Route('/', name: 'recipe_index')]
    public function index(): Response
    {
        return $this->render('recipe/index.html.twig');
    }


    #[Route('/recipe/add', name: 'recipe_add')]
    public function add(Request $request, Security $security, EntityManagerInterface $entityManager): Response
    {
        if (!empty($security->getUser())) {
            $recipe = new Recipe();
            $form = $this->createForm(RecipeType::class, $recipe);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $recipe->setTime(round($form->get('time')->getData()));
                $recipe->setStatus(Publish::PENDING);
                $entityManager->persist($recipe);
                $entityManager->flush();

                return $this->redirectToRoute('recipe_index');
            }

            return $this->render('recipe/add.html.twig', [
                'form' => $form,
                'recipe' => $recipe,
            ]);
        }
        $this->addFlash('error', 'You must be logged in to add a recipe.');
        return $this->redirectToRoute('app_login');
    }

    #[Route('/recipe/show/{uuid}', name: 'recipe_show')]
    public function show(#[MapEntity(mapping: ['uuid' => 'uuid'])] Recipe $recipe, UserRecipeRatingRepository $userRecipeRatingRepository, UserRecipeSavedRepository $userRecipeSavedRepository): Response
    {
        $recipeSavedByUser = $userRecipeSavedRepository->findOneBy(['recipe' => $recipe, 'user' => $this->getUser()]);
        $score = $userRecipeRatingRepository->findOneBy(['recipe' => $recipe, 'user' => $this->getUser()]);
        if (!empty($score)) {
            $score = $score->getScore();
        }
        return $this->render('recipe/show.html.twig', [
            'recipe' => $recipe,
            'score' => $score,
            'recipeSavedByUser' => $recipeSavedByUser,
        ]);
    }

    #[Route('/recipe/edit/{uuid}', name: 'recipe_edit')]
    public function edit(#[MapEntity(mapping: ['uuid' => 'uuid'])] Recipe $recipe, Request $request, EntityManagerInterface $entityManager, Security $security): Response
    {
        if ($security->getUser() === null) {
            $this->addFlash('error', 'You can only edit recipes that are owned by you.');
            return $this->redirectToRoute('recipe_show', ['uuid' => $recipe->getUuid()]);
        }
        if ($security->getUser() === $recipe->getCreatedBy() || $security->isGranted('ROLE_ADMIN')) {
            $form = $this->createForm(RecipeType::class, $recipe, ['edit' => true]);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                if (!$security->isGranted('ROLE_ADMIN')) {
                    $recipe->setStatus(Publish::PENDING);
                }
                $recipe->setTime(round($form->get('time')->getData()));
                $entityManager->persist($recipe);
                $entityManager->flush();
                $this->addFlash('success', 'Recipe updated successfully.');
                return $this->redirectToRoute('recipe_show', ['uuid' => $recipe->getUuid()]);
            }
            return $this->render('recipe/edit.html.twig', [
                'form' => $form,
                'recipe' => $recipe,
            ]);
        }
        $this->addFlash('error', 'You can only edit recipes that are owned by you.');
        return $this->redirectToRoute('recipe_show', ['uuid' => $recipe->getUuid()]);
    }

    #[Route('/recipe/save/{uuid}', name: 'recipe_save')]
    public function save(#[MapEntity(mapping: ['uuid' => 'uuid'])] Recipe $recipe, EntityManagerInterface $entityManager, UserRecipeSavedRepository $userRecipeSavedRepository, Security $security): Response
    {
        if (!empty($security->getUser())) {
            if ($recipe->getCreatedBy() === $security->getUser()) {
                $this->addFlash('error', 'You can only save recipes that are not owned by you.');
                return $this->redirectToRoute('recipe_show', ['uuid' => $recipe->getUuid()]);
            }
            $userRecipeSaved = $userRecipeSavedRepository->findOneBy(['recipe' => $recipe, 'user' => $security->getUser()]);
            if (!$userRecipeSaved) {
                $userRecipeSaved = new UserRecipeSaved();
                $userRecipeSaved->setRecipe($recipe);
                $userRecipeSaved->setUser($security->getUser());
                $entityManager->persist($userRecipeSaved);
                $this->addFlash('success', 'Recipe saved!');
            } else {
                $entityManager->remove($userRecipeSaved);
                $this->addFlash('success', 'Recipe removed from saved!');
            }
            $entityManager->flush();

            return $this->redirectToRoute('recipe_show', ['uuid' => $recipe->getUuid()]);
        }
        $this->addFlash('error', 'You must be logged in to save a recipe.');
        return $this->redirectToRoute('app_login');
    }

    #[Route('/my-recipes', name: 'recipe_saved')]
    public function saved(Security $security): Response
    {
        if ($security->getUser()) {
            return $this->render('my-recipes/index.html.twig');
        }
        $this->addFlash('error', 'You must be logged in!');
        return $this->redirectToRoute('app_login');
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
        $i = 1;
        foreach ($recipes as $recipe) {
            $session->set('recipe' . $i, $recipe->getUuid());
            $i++;
        }
        $session->set('count', $i - 1);

        return new JsonResponse([
            'redirectUrl' => $this->generateUrl('recipe_found'),
        ]);
    }

    #[Route('/recipe/found', name: 'recipe_found')]
    public function found(Request $request, RecipeRepository $recipeRepository): Response
    {
        $session = $request->getSession();
        $recipes = [];

        for ($i = 1; $i <= $session->get('count'); $i++) {
            $uuid = $session->get('recipe' . $i);
            $recipe = $recipeRepository->findOneBy(['uuid' => $uuid]);
            $recipes[] = $recipe;
        }

        return $this->render('recipe/found.html.twig', [
            'recipes' => $recipes,
        ]);
    }

    #[Route('/recipe/manage', name: 'recipe_manage')]
    public function manage(RecipeRepository $recipeRepository, Security $security): Response
    {
        if ($security->isGranted('ROLE_ADMIN')) {
            $recipes = $recipeRepository->findBy(['status' => Publish::PENDING]);
            return $this->render('recipe/manage.html.twig', [
                'recipes' => $recipes,
            ]);
        }
        $this->addFlash('error', 'You are not allowed to manage recipes.');
        return $this->redirectToRoute('recipe_index');

    }

    #[Route('/recipe/manage/reject/{uuid}', name: 'recipe_manage_reject')]
    public function reject(#[MapEntity(mapping: ['uuid' => 'uuid'])] Recipe $recipe, Security $security, EntityManagerInterface $entityManager): Response
    {
        if ($security->isGranted('ROLE_ADMIN')) {
            $entityManager->remove($recipe);
            $entityManager->flush();
            return $this->redirectToRoute('recipe_manage');
        }
        $this->addFlash('error', 'You are not authorized to accept or reject this recipe.');
        return $this->redirectToRoute('recipe_index');
    }

    #[Route('/recipe/manage/accept/{uuid}', name: 'recipe_manage_accept')]
    public function accept(#[MapEntity(mapping: ['uuid' => 'uuid'])] Recipe $recipe, Security $security, EntityManagerInterface $entityManager): Response
    {
        if ($security->isGranted('ROLE_ADMIN')) {
            $recipe->setStatus(Publish::ACCEPTED);
            $entityManager->persist($recipe);
            $entityManager->flush();
            return $this->redirectToRoute('recipe_manage');
        }
        $this->addFlash('error', 'You are not authorized to accept or reject this recipe.');
        return $this->redirectToRoute('recipe_index');
    }

    #[Route('/recipe/pdf/{uuid}/{people}', name: 'recipe_pdf'), ]
    public function pdf(#[MapEntity(mapping: ['uuid' => 'uuid'])] Recipe $recipe, #[MapEntity(mapping: ['people' => 'people'])] int $people, Environment $twig, Pdf $weasyPrint): PdfResponse
    {
        $html = $twig->render('recipe/pdf.html.twig', [
                'recipe' => $recipe,
                'people' => $people,
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
        if (!empty($security->getUser())) {
            if ($recipe->getCreatedBy() === $security->getUser() or $security->isGranted('ROLE_ADMIN')) {
                foreach ($recipe->getRecipeIngredients() as $ingredient) {
                    $entityManager->remove($ingredient);
                }
                $entityManager->remove($recipe);
                $entityManager->flush();
                $this->addFlash('success', 'Recipe has been deleted.');
                return $this->redirectToRoute('recipe_index');
            }
        }
        $this->addFlash('error', 'You can only delete recipes that are owned by you.');
        return $this->redirectToRoute('recipe_show', ['uuid' => $recipe->getUuid()]);
    }
}

