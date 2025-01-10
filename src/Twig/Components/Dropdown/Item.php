<?php

namespace App\Twig\Components\Dropdown;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent]
final class Item
{
    public string $label;
}
