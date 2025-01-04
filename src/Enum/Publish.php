<?php

namespace App\Enum;

use App\Enum\Trait\TranslatableTrait;
use Symfony\Contracts\Translation\TranslatableInterface;

enum Publish: string implements TranslatableInterface
{
    use TranslatableTrait;

    case PENDING = 'pending';
    case ACCEPTED = 'accepted';
}