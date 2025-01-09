<?php

namespace App\Repository;

use AllowDynamicProperties;
use App\Entity\Recipe;
use App\Enum\MealCountry;
use App\Enum\MealType;
use App\Enum\Publish;
use App\Service\QueryService;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\SecurityBundle\Security;

/**
 * @extends ServiceEntityRepository<Recipe>
 */
#[AllowDynamicProperties] class RecipeRepository extends ServiceEntityRepository
{
    private FoodGroupRepository $foodGroupRepository;

    public function __construct(ManagerRegistry $registry, FoodGroupRepository $foodGroupRepository, Security $security)
    {
        parent::__construct($registry, Recipe::class);
        $this->foodGroupRepository = $foodGroupRepository;
        $this->security = $security;
    }

    public function search(array $search, ?int $limit = null, bool $count = false, bool $myRecipes = false): array|int
    {
        if ($myRecipes) {
            $query = $this->createQueryBuilder('r')
                ->leftJoin('r.recipeIngredients', 'ri')
                ->leftJoin('ri.ingredient', 'i')
                ->leftJoin('r.userRecipeSaveds', 's')
                ->orWhere('s.user = :user')
                ->setParameter('user', $this->security->getUser())
                ->orWhere('r.createdBy = :user')
                ->setParameter('user', $this->security->getUser());
        } else {
            $query = $this->createQueryBuilder('r')
                ->leftJoin('r.recipeIngredients', 'ri')
                ->leftJoin('ri.ingredient', 'i')
                ->andWhere('r.status = :status')
                ->setParameter('status', Publish::ACCEPTED)
                ->andWhere('r.publish = 1');
        }

        if (QueryService::isNotEmpty($search, 'name')) {
            $query
                ->andWhere('r.name LIKE :name')
                ->setParameter('name', '%' . $search['name'] . '%');
        }

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

        if (!empty($search)) {
            $inGroup = [];
            $notInGroup = [];
            if (QueryService::isNotEmpty($search, 'meat')) {
                if (!empty($meat = $this->foodGroupRepository->findOneBy(['name' => 'Meat']))) {
                    $inGroup[] = $meat->getId();
                }
            }
            if (QueryService::isNotEmpty($search, 'seaFood')) {
                if (!empty($fish = $this->foodGroupRepository->findOneBy(['name' => 'Fish and Seafood']))) {
                    $inGroup[] = $fish->getId();
                }
            }
            if (QueryService::isNotEmpty($search, 'proteinFoods')) {
                if (!empty($proteins = $this->foodGroupRepository->findOneBy(['name' => 'Proteins']))) {
                    $inGroup[] = $proteins->getId();
                }
            }
            if (QueryService::isNotEmpty($search, 'pastas')) {
                if (!empty($pasta = $this->foodGroupRepository->findOneBy(['name' => 'Pasta']))) {
                    $inGroup[] = $pasta->getId();
                }
            }
            if (QueryService::isNotEmpty($search, 'vegetarian')) {
                if (!empty($meat = $this->foodGroupRepository->findOneBy(['name' => 'Meat']))) {
                    $notInGroup[] = $meat->getId();
                }
                if (!empty($fish = $this->foodGroupRepository->findOneBy(['name' => 'Fish and Seafood']))) {
                    $notInGroup[] = $fish->getId();
                }
            }
            if (QueryService::isNotEmpty($search, 'nuts')) {
                if (!empty($nuts = $this->foodGroupRepository->findOneBy(['name' => 'Nuts']))) {
                    $notInGroup[] = $nuts->getId();
                }
            }
            if (QueryService::isNotEmpty($search, 'gluten')) {
                if (!empty($grains = $this->foodGroupRepository->findOneBy(['name' => 'Grains and Cereals']))) {
                    $notInGroup[] = $grains->getId();
                }
                if (!empty($baked = $this->foodGroupRepository->findOneBy(['name' => 'Baked Goods']))) {
                    $notInGroup[] = $baked->getId();
                }
                if (!empty($pasta = $this->foodGroupRepository->findOneBy(['name' => 'Pasta']))) {
                    $notInGroup[] = $pasta->getId();
                }
            }
            if (QueryService::isNotEmpty($search, 'seaFoodAllergies')) {
                if (!empty($fish = $this->foodGroupRepository->findOneBy(['name' => 'Fish and Seafood']))) {
                    $notInGroup[] = $fish->getId();
                }
            }
            if (QueryService::isNotEmpty($search, 'dairy')) {
                if (!empty($dairy = $this->foodGroupRepository->findOneBy(['name' => 'Dairy']))) {
                    $notInGroup[] = $dairy->getId();
                }
            }

            if (!empty($inGroup)) {
                $number = count($inGroup);
                $query
                    ->andWhere('r.id IN (SELECT r2.id FROM App\Entity\Recipe r2 LEFT JOIN r2.recipeIngredients ri2 LEFT JOIN ri2.ingredient i2 LEFT JOIN i2.foodgroup fg2 WHERE fg2.id IN (:includedGroup) GROUP BY r2.id HAVING COUNT(DISTINCT fg2.id) = :number)')
                    ->setParameter('includedGroup', $inGroup)
                    ->setParameter('number', $number);
            }
            if (!empty($notInGroup)) {
                $query
                    ->andWhere('r.id NOT IN (SELECT r3.id FROM App\Entity\Recipe r3 LEFT JOIN r3.recipeIngredients ri3 LEFT JOIN ri3.ingredient i3 LEFT JOIN i3.foodgroup fg3 WHERE fg3.id IN (:excludedGroup))')
                    ->setParameter('excludedGroup', $notInGroup);
            }

            $foodGroup = [];
            if (QueryService::isNotEmpty($search, 'breakfast')) {
                $foodGroup[] = MealType::BREAKFAST->value;
            }
            if (QueryService::isNotEmpty($search, 'brunch')) {
                $foodGroup[] = MealType::BRUNCH->value;
            }
            if (QueryService::isNotEmpty($search, 'lunch')) {
                $foodGroup[] = MealType::LUNCH->value;
            }
            if (QueryService::isNotEmpty($search, 'dinner')) {
                $foodGroup[] = MealType::DINNER->value;
            }
            if (QueryService::isNotEmpty($search, 'cocktail')) {
                $foodGroup[] = MealType::COCKTAIL->value;
            }

            if (!empty($foodGroup)) {
                $query
                    ->andWhere('r.mealType IN (:foodGroup)')
                    ->setParameter('foodGroup', $foodGroup);
            }

            $country = [];
            if (QueryService::isNotEmpty($search, 'italian')) {

                $country[] = MealCountry::ITALIAN->value;
            }
            if (QueryService::isNotEmpty($search, 'mexican')) {
                $country[] = MealCountry::MEXICAN->value;
            }
            if (QueryService::isNotEmpty($search, 'greek')) {
                $country[] = MealCountry::GREEK->value;
            }
            if (QueryService::isNotEmpty($search, 'chinese')) {
                $country[] = MealCountry::CHINESE->value;
            }
            if (QueryService::isNotEmpty($search, 'japanese')) {
                $country[] = MealCountry::JAPANESE->value;
            }
            if (QueryService::isNotEmpty($search, 'french')) {
                $country[] = MealCountry::FRENCH->value;
            }
            if (QueryService::isNotEmpty($search, 'american')) {
                $country[] = MealCountry::AMERICAN->value;
            }

            if (!empty($country)) {
                $query
                    ->andWhere('r.country IN (:country)')
                    ->setParameter('country', $country);
            }
        }

        if ($count) {
            return $query
                ->groupBy('r.id')
                ->getQuery()
                ->getResult();
        }

        return $query
            ->orderBy('r.createdAt')
            ->groupBy('r.id')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    public function findMeal(string $meal = null, string $country, string $difficulty, string $mealType)
    {

        $query = $this->createQueryBuilder('r')
            ->leftJoin('r.recipeIngredients', 'ri')
            ->leftJoin('ri.ingredient', 'i')
            ->groupBy('r.id');

        if (MealCountry::tryFrom($country)) {
            $query
                ->andWhere('r.country = :country')
                ->setParameter('country', $country);
        }
        if (MealType::tryFrom($mealType)) {
            $query
                ->andWhere('r.mealType = :mealType')
                ->setParameter('mealType', $mealType);
        }

        if (!empty($meal)) {
            if ($meal == 'vegetarian') {
                $foodGroup = [];
                $meat = $this->foodGroupRepository->findOneBy(['name' => 'Meat']);
                $fish = $this->foodGroupRepository->findOneBy(['name' => 'Fish and Seafood']);
                $foodGroup[] = $meat->getId();
                $foodGroup[] = $fish->getId();
                $query
                    ->andWhere('r.id NOT IN (SELECT r1.id FROM App\Entity\Recipe r1 LEFT JOIN r1.recipeIngredients ri1 LEFT JOIN ri1.ingredient i1 LEFT JOIN i1.foodgroup fg1 WHERE fg1.id IN (:mealTypeExcluded) GROUP BY r1.id)')
                    ->setParameter('mealTypeExcluded', $foodGroup);
            } else {
                $foodGroup = $this->foodGroupRepository->findOneBy(['name' => $meal]);
                $query
                    ->andWhere('r.id IN (SELECT r1.id FROM App\Entity\Recipe r1 LEFT JOIN r1.recipeIngredients ri1 LEFT JOIN ri1.ingredient i1 LEFT JOIN i1.foodgroup fg1 WHERE fg1.id IN (:mealTypeIncluded) GROUP BY r1.id)')
                    ->setParameter('mealTypeIncluded', $foodGroup->getId());
            }
        }


        if ($difficulty == 'easy') {
            $query
                ->andWhere('r.time <= :time')
                ->setParameter('time', 15);
        }
        if ($difficulty == 'normal') {
            $query
                ->andWhere('r.time < :maxTime')
                ->setParameter('maxTime', 45)
                ->andWhere('r.time > :minTime')
                ->setParameter('minTime', 15);
        }
        if ($difficulty == 'hard') {
            $query
                ->andWhere('r.time >= :hardTime')
                ->setParameter('hardTime', 45);
        }

        return $query
            ->getQuery()
            ->getResult();
    }
}
