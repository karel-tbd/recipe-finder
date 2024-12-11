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
            'h2' => 'text-xl',
            'h3' => 'text-lg',
            'h4' => 'text-base',
            default => throw new LogicException(sprintf('Unknown button type "%s"', $this->tag)),
        };
    }

    #[PostMount]
    function postMount(): void
    {
        $this->extraClasses .= ' ' . $this->getSize();
    }
}
