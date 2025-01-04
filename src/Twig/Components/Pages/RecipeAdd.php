<?php

namespace App\Twig\Components\Pages;

use App\Entity\Recipe as RecipeEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveArg;
use Symfony\UX\LiveComponent\Attribute\LiveListener;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\ComponentToolsTrait;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent]
final class RecipeAdd extends AbstractController
{
    use DefaultActionTrait;
    use ComponentToolsTrait;

    #[LiveProp(fieldName: 'formData')]
    public ?RecipeEntity $recipe = null;

    #[LiveProp(writable: true)]
    public ?array $recipeValues = [];

    #[LiveProp(writable: true)]
    public int $step = 1;

    #[LiveListener('next')]
    public function next(#[LiveArg] array $values): void
    {
        $this->recipeValues = array_merge($this->recipeValues, $values);
        $this->step++;

        if ($this->step == 4) {
            dd($this->recipeValues);
        }
    }

    #[LiveListener('prev')]
    public function prev(): void
    {
        $this->step--;
    }
}
