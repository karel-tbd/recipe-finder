<?php

namespace App\Twig\Components\Table;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent]
final class Cell
{
    public string $tag = 'th';
    public ?string $value;
}
