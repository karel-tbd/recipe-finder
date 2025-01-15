<?php

namespace App\Twig\Components\Pages;

use App\Entity\Recipe as RecipeEntity;
use App\Form\RecipeType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\DefaultActionTrait;
use Symfony\UX\LiveComponent\LiveCollectionTrait;

#[AsLiveComponent]
final class Recipe extends AbstractController
{
    use DefaultActionTrait;
    use LiveCollectionTrait;

    public bool $edit = false;
    #[LiveProp(writable: true, fieldName: 'formData')]
    public ?RecipeEntity $recipe = null;

    protected function instantiateForm(): FormInterface
    {
        return $this->createForm(RecipeType::class, $this->recipe);
    }
}
