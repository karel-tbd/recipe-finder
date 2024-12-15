<?php

namespace App\Service;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\QueryBuilder;

class QueryService
{
    static function isNotEmpty(array $search, string $key): bool
    {
        // If the key does not exist in the search array, return false
        if (!array_key_exists($key, $search)) return false;
        // If the key is an ArrayCollection, return whether it is empty
        if ($search[$key] instanceof ArrayCollection) return !$search[$key]->isEmpty();

        return !empty($search[$key]);
    }

    /**
     * Add a left join to the query if the alias does not exist
     *
     * @param QueryBuilder $query
     * @param string $target
     * @param string $alias
     */
    static function leftJoin(QueryBuilder $query, string $target, string $alias): void
    {
        if (!in_array($alias, $query->getAllAliases())) {
            $query->leftJoin($target, $alias);
        }
    }
}
