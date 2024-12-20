<?php

namespace App\Twig\Components;

use LogicException;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;
use Symfony\UX\TwigComponent\Attribute\PostMount;

#[AsTwigComponent]
final class Button
{
    public string $label;
    public string $size = 'md';
    public string $tag = 'button';
    public string $type = 'submit';
    public string $variant = 'primary';
    public string $extraClasses = '';
    public bool $fullWidth = false;

    public function getSizeClasses(): string
    {
        return match ($this->size) {
            'sm' => 'p-2',
            'md' => 'px-4 py-2',
            'lg' => 'px-6 py-4',
            default => throw new LogicException(sprintf('Unknown button size "%s"', $this->size)),
        };
    }

    public function getVariantClasses(): string
    {
        return match ($this->variant) {
            'primary' => 'bg-red-500 p-2 rounded hover:bg-red-400 font-bold text-base tracking-wider whitespace-nowrap text-white	',
            'white' => 'border border-black p-1 text-black rounded hover:bg-gray-100 font-bold text-base tracking-wider whitespace-nowrap',
            default => throw new LogicException(sprintf('Unknown button type "%s"', $this->variant)),
        };
    }

    #[PostMount]
    function postMount(): void
    {
        $this->extraClasses = $this->fullWidth ? 'w-full' : '';
        $this->extraClasses .= ' ' . $this->getVariantClasses();
        $this->extraClasses .= ' ' . $this->getSizeClasses();
    }
}
