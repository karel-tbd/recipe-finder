<?php

namespace App\Enum;

use App\Enum\Trait\TranslatableTrait;
use Symfony\Contracts\Translation\TranslatableInterface;

enum MealCountry: string implements TranslatableInterface
{
    use TranslatableTrait;

    case ITALIAN = 'italian';
    case MEXICAN = 'mexican';
    case GREEK = 'greek';
    case CHINESE = 'chinese';
    case JAPANESE = 'japanese';
    case FRENCH = "french";
    case AMERICAN = 'american';


}