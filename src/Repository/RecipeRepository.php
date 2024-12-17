<?php

namespace App\Repository;

use App\Entity\Ingredients;
use App\Entity\Recipe;
use App\Service\QueryService;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Recipe>
 */
class RecipeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Recipe::class);
    }

    public function search(array $search = []): array
    {
        $query = $this->createQueryBuilder('r')
        ->join('r.ingredients', 'i');

        if (QueryService::isNotEmpty($search, 'search')) {
            $number = count($search['search']);
                $query
                    ->andWhere('i.id IN (:ingredients)')
                    ->setParameter('ingredients', $search['search'])
                    ->groupBy('r.id')
                    ->having('COUNT(r.id) =  '.$number)
                ;
            }

        return $query
            ->getQuery()
            ->getResult();
    }
}
