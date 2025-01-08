<?php

namespace App\Twig\Components;

use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;
use Symfony\UX\TwigComponent\Attribute\PostMount;

#[AsTwigComponent]
final class Dropdown
{
    public UuidInterface $uuid;
    public bool $isHidden = true;
    public string $label;

    public function __construct(public readonly RequestStack $requestStack)
    {
    }

    #[PostMount]
    public function postMount(): void
    {
        $this->uuid = Uuid::uuid4();
    }
}
