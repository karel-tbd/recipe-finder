<?php

namespace App\Enum;

use App\Enum\Trait\TranslatableTrait;
use Symfony\Contracts\Translation\TranslatableInterface;

enum Unit: string implements TranslatableInterface
{
    use TranslatableTrait;

    case GRAMS = 'grams';
    case KILOGRAM = 'kilo';
    case TABLESPOON = 'tablespoon';
    case TEASPOON = 'teaspoon';
    case CLOVES = "cloves";
    case MILILITERS = "mililiters";
    case LITERS = "liters";
    case SLICES = "slices";
    case LEAVES = "leaves";
    case PIECES = "pieces";

}