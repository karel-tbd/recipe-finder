<?php

namespace App\Repository;

use App\Entity\Recipe;
use App\Entity\User;
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

    public function search(array $search = [], array $filter = []): array
    {
        $query = $this->createQueryBuilder('r')
            ->leftJoin('r.recipeIngredients', 'ri')
            ->leftJoin('ri.ingredient', 'i');

        if (QueryService::isNotEmpty($search, 'search')) {
            $number = count($search['search']);
            $query
                ->andWhere('ri.id IN (:ingredients)')
                ->setParameter('ingredients', $search['search'])
                ->groupBy('r.id')
                ->having('COUNT(r.id) =  ' . $number);
        }

        if (QueryService::isNotEmpty($filter, 'vegetarian')) {
            $query
                ->andWhere('i.foodGroup NOT IN (5, 6)');
            dd($query->getQuery()->getResult());
        }

        return $query
            ->getQuery()
            ->getResult();
    }

    public function saved(User $user, array $search = []): array
    {
        $query = $this->createQueryBuilder('r')
            ->join('r.recipeIngredients', 'i')
            ->leftJoin('r.userRecipeSaveds', 's');


        $query
            ->where('r.createdBy = :user')
            ->setParameter('user', $user)
            ->orWhere('s.user = :user')
            ->setParameter('user', $user);

        if (QueryService::isNotEmpty($search, 'search')) {
            $number = count($search['search']);
            $query
                ->andWhere('i.id IN (:ingredients)')
                ->setParameter('ingredients', $search['search'])
                ->groupBy('r.id')
                ->having('COUNT(r.id) =  ' . $number);
        }

        return $query
            ->getQuery()
            ->getResult();
    }
}
