<?php

namespace App\Commands;

use App\Entity\Ingredients;
use App\Entity\Recipe;
use App\Repository\FoodGroupRepository;
use App\Repository\IngredientsRepository;
use App\Repository\RecipeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:import-recipes',
)]
class ImportRecipes extends Command
{

public function __construct(private readonly string                 $kernelProjectDir,
                            private readonly EntityManagerInterface $entityManager,
                            private readonly RecipeRepository       $recipeRepository,
                            private readonly IngredientsRepository $ingredientsRepository,
                            string                                  $name = null)
{
    parent::__construct($name);
}

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $filename = $this->kernelProjectDir . '/private/imports/recipes.txt';

        $file = fopen($filename, "r");
        $i = 0;
        $publish = true;
        while (($data = fgetcsv($file, 10000, ";")) !== FALSE) {
            if ($i >= 0) {
                    $recipe = new Recipe();
                    $recipe->setName($data[0]);
                    $ingredients = explode(",", $data[1]);
                    foreach ($ingredients as $ingredient) {
                        $ingredient = trim($ingredient);
                        $knownIngredient = $this->ingredientsRepository->findOneBy(['name' => $ingredient]);
                        if ($knownIngredient) {
                            $recipe->addIngredient($knownIngredient);
                        }else{
                            $publish = false;
                            $output->writeln('ingredient '. $ingredient.' not found');
                        }
                    }
                    $recipe->setDescription($data[2]);
                    $recipe->setTime($data[3]);

                    if ($publish) {
                        $this->entityManager->persist($recipe);
                        $output->writeln('recipe '. $recipe->getName().' is valid');
                    }
                    else{
                        $output->writeln('recipe '. $recipe->getName().' has invalid ingredient');
                    }
            }
            $i++;
        }
        $this->entityManager->flush();

        return Command::SUCCESS;
    }
}