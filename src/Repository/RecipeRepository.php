<?php

namespace App\Repository;

use App\Entity\Recipe;
use App\Entity\User;
use App\Enum\MealType;
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

        if (QueryService::isNotEmpty($search, 'meat')) {
            if (!empty($meat = $this->foodGroupRepository->findOneBy(['name' => 'Meat']))) {
                $meatGroup = $meat->getId();
            }
            if (!empty($meatGroup)) {
                $query
                    ->andWhere('r.id IN (SELECT r2.id FROM App\Entity\Recipe r2 LEFT JOIN r2.recipeIngredients ri2 LEFT JOIN ri2.ingredient i2 LEFT JOIN i2.foodGroup fg2 WHERE fg2.id IN (:meat))')
                    ->setParameter('meat', $meatGroup);
            }
        }

        if (QueryService::isNotEmpty($search, 'seaFood')) {
            if (!empty($fish = $this->foodGroupRepository->findOneBy(['name' => 'Fish and Seafood']))) {
                $fishGroup = $fish->getId();
            }
            if (!empty($fishGroup)) {
                $query
                    ->andWhere('r.id IN (SELECT r2.id FROM App\Entity\Recipe r2 LEFT JOIN r2.recipeIngredients ri2 LEFT JOIN ri2.ingredient i2 LEFT JOIN i2.foodGroup fg2 WHERE fg2.id IN (:fish))')
                    ->setParameter('fish', $fishGroup);
            }
        }

        if (QueryService::isNotEmpty($search, 'proteinFoods')) {
            if (!empty($proteins = $this->foodGroupRepository->findOneBy(['name' => 'Proteins']))) {
                $proteinGroup = $proteins->getId();
            }
            if (!empty($proteinGroup)) {
                $query
                    ->andWhere('r.id IN (SELECT r2.id FROM App\Entity\Recipe r2 LEFT JOIN r2.recipeIngredients ri2 LEFT JOIN ri2.ingredient i2 LEFT JOIN i2.foodGroup fg2 WHERE fg2.id IN (:protein))')
                    ->setParameter('protein', $proteinGroup);
            }
        }

        if (QueryService::isNotEmpty($search, 'pastas')) {
            if (!empty($pasta = $this->foodGroupRepository->findOneBy(['name' => 'Pasta']))) {
                $pastaGroup = $pasta->getId();
            }
            if (!empty($pastaGroup)) {
                $query
                    ->andWhere('r.id IN (SELECT r2.id FROM App\Entity\Recipe r2 LEFT JOIN r2.recipeIngredients ri2 LEFT JOIN ri2.ingredient i2 LEFT JOIN i2.foodGroup fg2 WHERE fg2.id IN (:pasta))')
                    ->setParameter('pasta', $pastaGroup);
            }
        }

        if (QueryService::isNotEmpty($search, 'breakfast')) {
            $query
                ->andWhere('r.mealType = :mealType')
                ->setParameter('mealType', MealType::BREAKFAST);
        }

        if (QueryService::isNotEmpty($search, 'brunch')) {
            $query
                ->andWhere('r.mealType = :mealType')
                ->setParameter('mealType', MealType::BRUNCH);
        }

        if (QueryService::isNotEmpty($search, 'lunch')) {
            $query
                ->andWhere('r.mealType = :mealType')
                ->setParameter('mealType', MealType::LUNCH);
        }


        if (QueryService::isNotEmpty($search, 'dinner')) {
            $query
                ->andWhere('r.mealType = :mealType')
                ->setParameter('mealType', MealType::DINNER);
        }

        if (QueryService::isNotEmpty($search, 'cocktail')) {
            $query
                ->andWhere('r.mealType = :mealType')
                ->setParameter('mealType', MealType::COCKTAIL);
        }

        if (QueryService::isNotEmpty($search, 'nuts')) {
            if (!empty($nuts = $this->foodGroupRepository->findOneBy(['name' => 'Nuts']))) {
                $nutGroup = $nuts->getId();
            }
            if (!empty($nutGroup)) {
                $query
                    ->andWhere('r.id NOT IN (SELECT r2.id FROM App\Entity\Recipe r2 LEFT JOIN r2.recipeIngredients ri2 LEFT JOIN ri2.ingredient i2 LEFT JOIN i2.foodGroup fg2 WHERE fg2.id IN (:nuts))')
                    ->setParameter('nuts', $nutGroup);
            }
        }

        if (QueryService::isNotEmpty($search, 'gluten')) {
            $gluten = [];
            if (!empty($grains = $this->foodGroupRepository->findOneBy(['name' => 'Grains and Cereals']))) {
                $gluten[] = $grains->getId();
            }
            if (!empty($baked = $this->foodGroupRepository->findOneBy(['name' => 'Baked Goods']))) {
                $gluten[] = $baked->getId();
            }
            if (!empty($pasta = $this->foodGroupRepository->findOneBy(['name' => 'Pasta']))) {
                $gluten[] = $pasta->getId();
            }
            if (!empty($gluten)) {
                $query
                    ->andWhere('r.id NOT IN (SELECT r2.id FROM App\Entity\Recipe r2 LEFT JOIN r2.recipeIngredients ri2 LEFT JOIN ri2.ingredient i2 LEFT JOIN i2.foodGroup fg2 WHERE fg2.id IN (:gluten))')
                    ->setParameter('gluten', $gluten);
            }
        }

        if (QueryService::isNotEmpty($search, 'seaFoodAllergies')) {
            if (!empty($fish = $this->foodGroupRepository->findOneBy(['name' => 'Fish and Seafood']))) {
                $fishGroup = $fish->getId();
            }
            if (!empty($fishGroup)) {
                $query
                    ->andWhere('r.id NOT IN (SELECT r2.id FROM App\Entity\Recipe r2 LEFT JOIN r2.recipeIngredients ri2 LEFT JOIN ri2.ingredient i2 LEFT JOIN i2.foodGroup fg2 WHERE fg2.id IN (:fish))')
                    ->setParameter('fish', $fishGroup);
            }
        }

        if (QueryService::isNotEmpty($search, 'dairy')) {
            if (!empty($dairy = $this->foodGroupRepository->findOneBy(['name' => 'Dairy']))) {
                $dairyGroup = $dairy->getId();
            }
            if (!empty($dairyGroup)) {
                $query
                    ->andWhere('r.id NOT IN (SELECT r2.id FROM App\Entity\Recipe r2 LEFT JOIN r2.recipeIngredients ri2 LEFT JOIN ri2.ingredient i2 LEFT JOIN i2.foodGroup fg2 WHERE fg2.id IN (:dairy))')
                    ->setParameter('dairy', $dairyGroup);
            }
        }

        return $query
            ->getQuery()
            ->getResult();
    }

    public function saved(User $user, array $search = []): array
    {
        $query = $this->createQueryBuilder('r')
            ->leftJoin('r.userRecipeSaved', 's');
        $query
            ->where('r.createdBy = :user')
            ->setParameter('user', $user);

        /*if (QueryService::isNotEmpty($search, 'search')) {
            $number = count($search['search']);
            $query
                ->andWhere('ri.id IN (:ingredients)')
                ->setParameter('ingredients', $search['search'])
                ->groupBy('r.id')
                ->having('COUNT(r.id) =  ' . $number);
        }*/

        return $query
            ->getQuery()
            ->getResult();
    }
}
