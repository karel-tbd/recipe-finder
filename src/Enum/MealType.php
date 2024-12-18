<?php

namespace App\Enum;

use App\Enum\Trait\TranslatableTrait;
use Symfony\Contracts\Translation\TranslatableInterface;

enum MealType: string implements TranslatableInterface
{
    use TranslatableTrait;

    case BREAKFAST = 'breakfast';
    case BRUNCH = 'brunch';
    case LUNCH = 'lunch';
    case DINNER = 'dinner';
    case DESSERT = "dessert";
    case COCKTAIL = "cocktail";
}