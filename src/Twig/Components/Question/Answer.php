<?php

namespace App\Twig\Components\Question;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent]
final class Answer
{
    public string $dataPosition;
    public string $dataTarget;
}
