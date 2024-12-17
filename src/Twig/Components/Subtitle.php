<?php

namespace App\Twig\Components;

use LogicException;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;
use Symfony\UX\TwigComponent\Attribute\PostMount;

#[AsTwigComponent]
final class Subtitle
{
    public string $extraClasses = '';
    public string $tag = 'h2';

    public function getSize(): string
    {
        return match ($this->tag) {
            'h2' => 'text-3xl',
            'h3' => 'text-2xl',
            'h4' => 'text-xl',
            default => throw new LogicException(sprintf('Unknown button type "%s"', $this->tag)),
        };
    }

    #[PostMount]
    function postMount(): void
    {
        $this->extraClasses .= ' ' . $this->getSize();
    }
}
