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
    private FoodGroupRepository $foodGroupRepository;

    public function __construct(ManagerRegistry $registry, FoodGroupRepository $foodGroupRepository)
    {
        parent::__construct($registry, Recipe::class);
        $this->foodGroupRepository = $foodGroupRepository;
    }

    public function search(array $search = []): array
    {

        $query = $this->createQueryBuilder('r')
            ->leftJoin('r.recipeIngredients', 'ri')
            ->leftJoin('ri.ingredient', 'i')
            ->groupBy('r.id');

        if (QueryService::isNotEmpty($search, 'search')) {
            $ingredients = reset($search['search']);
            $number = count($ingredients);
//            $query
//                ->andWhere('i.name LIKE (:ingredient)')
//                ->setParameter('ingredient', $search['search'])
//                ->having('COUNT(r.id) =  ' . $number);

            //poging tot zoeken op naam
            $or = [];
            foreach ($ingredients as $i => $ingredient) {
                $or[] = 'i.name LIKE :ingredient' . $i;
                $query->setParameter('ingredient' . $i, '%' . $ingredient->getName() . '%');
            }
            if (!empty($or)) {
                $query->andWhere(implode(' OR ', $or))
                    ->having('COUNT(r.id) >= :number')
                    ->setParameter('number', $number);
            }
        }

        if (QueryService::isNotEmpty($search, 'vegetarian')) {
            $vegetarian = [];
            if (!empty($meat = $this->foodGroupRepository->findOneBy(['name' => 'Meat']))) {
                $vegetarian[] = $meat->getId();
            }
            if (!empty($fish = $this->foodGroupRepository->findOneBy(['name' => 'Fish and Seafood']))) {
                $vegetarian[] = $fish->getId();
            }
            if (!empty($vegetarian)) {
                $query
                    ->andWhere('r.id NOT IN (SELECT r2.id FROM App\Entity\Recipe r2 LEFT JOIN r2.recipeIngredients ri2 LEFT JOIN ri2.ingredient i2 LEFT JOIN i2.foodGroup fg2 WHERE fg2.id IN (:vegetarian))')
                    ->setParameter('vegetarian', $vegetarian);
            }
        }

        return $query
            ->getQuery()
            ->getResult();
    }

    public function saved(User $user, array $search = []): array
    {
        $query = $this->createQueryBuilder('r')
            ->join('r.recipeIngredients', 'ri')
            ->leftJoin('r.userRecipeSaveds', 's');


        $query
            ->where('r.createdBy = :user')
            ->setParameter('user', $user)
            ->orWhere('s.user = :user')
            ->setParameter('user', $user);

        if (QueryService::isNotEmpty($search, 'search')) {
            $number = count($search['search']);
            $query
                ->andWhere('ri.id IN (:ingredients)')
                ->setParameter('ingredients', $search['search'])
                ->groupBy('r.id')
                ->having('COUNT(r.id) =  ' . $number);
        }

        return $query
            ->getQuery()
            ->getResult();
    }
}
