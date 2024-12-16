<?php

namespace App\Twig\Components;

use LogicException;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;
use Symfony\UX\TwigComponent\Attribute\PostMount;

#[AsTwigComponent]
final class Link
{
    public string $label;
    public string $size = 'md';
    public string $tag = 'a';
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
            'primary' => 'flex items-center py-1 bg-blue-200 mt-5 rounded hover:bg-blue-300 font-bold text-base tracking-wider h-fit w-fit',
            'white' => 'flex items-center py-1 border text-center border-secondary mt-5 text-secondary rounded hover:bg-gray-100 font-bold text-base tracking-wider h-fit',
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
