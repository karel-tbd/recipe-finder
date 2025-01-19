<?php

namespace App\Commands;

use App\Entity\Ingredients;
use App\Repository\FoodGroupRepository;
use App\Repository\IngredientsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:import',
)]
class Import extends Command
{

    public function __construct(private readonly string                 $kernelProjectDir,
                                private readonly FoodGroupRepository    $foodGroupRepository,
                                private readonly EntityManagerInterface $entityManager,
                                private readonly IngredientsRepository  $ingredientsRepository,

                                string                                  $name = null)
    {
        parent::__construct($name);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $filename = $this->kernelProjectDir . '/private/imports/seeds.txt';

        $file = fopen($filename, "r");
        $foodgroup = $this->foodGroupRepository->find(19);

        while (($data = fgetcsv($file, 100, ",")) !== FALSE) {
            $ingredient = $this->ingredientsRepository->findOneBy(['name' => $data[0]]);
            if (!$ingredient) {
                $ingredient = new Ingredients();
                $ingredient->setName($data[0]);
            }
            $ingredient->addFoodgroup($foodgroup);
            $this->entityManager->persist($ingredient);
        }
        $this->entityManager->flush();

        return Command::SUCCESS;
    }
}