<?php

namespace App\Twig\Components\Sidebar;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent]
final class Item
{
    public string $path;
    public string $icon;
    public string $label;
    public array $active = [];
}
