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

public function __construct(private readonly string $kernelProjectDir,
                            private readonly FoodGroupRepository $foodGroupRepository,
                            private readonly EntityManagerInterface $entityManager,
                            private readonly IngredientsRepository $ingredientsRepository,
                            string $name = null)
{
    parent::__construct($name);
}

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $filename = $this->kernelProjectDir . '/private/imports/pastas.txt';

        $file = fopen($filename, "r");
        $i = 0;
        while (($data = fgetcsv($file, 100, ",")) !== FALSE) {
        $foodgroup = $this->foodGroupRepository->find(18);
            if ($i >= 0) {
                    $ingridient = new Ingredients();
                    $ingridient->setName($data[0]);
                    $ingridient->setFoodGroup($foodgroup);
                    $this->entityManager->persist($ingridient);
            }
            $i++;
        }

        $this->entityManager->flush();

        return Command::SUCCESS;
    }
}