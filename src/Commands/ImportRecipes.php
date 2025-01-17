<?php

namespace App\Commands;

use App\Entity\Recipe;
use App\Entity\RecipeIngredients;
use App\Enum\MealCountry;
use App\Enum\MealType;
use App\Enum\Publish;
use App\Enum\Unit;
use App\Repository\IngredientsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ImportRecipes extends Command
{

    public function __construct(private readonly string                 $kernelProjectDir,
                                private readonly EntityManagerInterface $entityManager,
                                private readonly IngredientsRepository  $ingredientsRepository,
    )
    {
        parent::__construct('app:import-recipes');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $filename = $this->kernelProjectDir . '/private/imports/__recipes.txt';

        $file = fopen($filename, "r");
        $publish = true;

        while (($data = fgetcsv($file, 10000, ";")) !== FALSE) {
            $recipe = new Recipe();
            $invalidIngredients = [];
            $recipe->setName($data[0]);
            $recipe->setDescription($data[2]);
            $recipe->setTime($data[3]);
            $recipe->setInstructions($data[4]);
            $recipe->setPeople($data[5]);
            $recipe->setPublish(true);
            $recipe->setStatus(Publish::ACCEPTED);
            if (!empty($data[6])) {
                $recipe->setMealType([MealType::tryFrom(trim($data[6]))]);
            }
            if (!empty($data[7])) {
                $recipe->setCountry(MealCountry::tryFrom(trim($data[7])));
            }
            $this->entityManager->persist($recipe);
            $ingredients = explode(",", $data[1]);

            foreach ($ingredients as $ingredientInfo) {
                $recipeIngredient = new RecipeIngredients();
                $ingredient = explode(':', trim($ingredientInfo));
                $ingredientName = $ingredient[0];
                $ingredientQuantity = $ingredient[1];
                $ingredientUnit = $ingredient[2];
                $knownIngredient = $this->ingredientsRepository->findOneBy(['name' => $ingredientName]);

                if ($knownIngredient) {
                    $recipeIngredient->setRecipe($recipe);
                    $recipeIngredient->setIngredient($knownIngredient);
                    $recipeIngredient->setQuantity($ingredientQuantity);
                    $recipeIngredient->setUnit(Unit::tryFrom($ingredientUnit));
                } else {
                    $invalidIngredients[] = $ingredientName;
                    $publish = false;
                }

                $this->entityManager->persist($recipeIngredient);
            }

            if ($publish) {
                $this->entityManager->flush();
                $output->writeln('recipe ' . $recipe->getName() . ' is valid');
            } else {
                $output->writeln('recipe ' . $recipe->getName() . ' has invalid ingredient');
                foreach ($invalidIngredients as $invalidIngredient) {
                    $output->writeln($invalidIngredient);
                }
            }

            $this->entityManager->clear();

        }

        return Command::SUCCESS;
    }
}