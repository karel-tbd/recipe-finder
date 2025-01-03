<?php

namespace App\Twig\Components\Pages;

use App\Entity\Recipe;
use App\Entity\UserRecipeSaved;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveArg;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\DefaultActionTrait;
use Symfony\UX\TwigComponent\Attribute\PostMount;

#[AsLiveComponent]
final class ShowRecipe
{
    use DefaultActionTrait;

    #[LiveProp]
    public Recipe $recipe;

    #[LiveProp]
    public int $people;

    #[LiveProp]
    public ?UserRecipeSaved $recipeSavedByUser;

    #[LiveProp]
    public ?int $score;

    #[PostMount]
    public function postMount(): void
    {
        $this->people = $this->recipe->getPeople();
    }

    #[LiveAction]
    public function updatePeople(#[LiveArg] int $people)
    {
        $this->people = $people;
    }

}
