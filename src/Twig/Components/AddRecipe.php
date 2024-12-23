<?php

namespace App\Twig\Components;

use App\Form\RecipeType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\ComponentWithFormTrait;
use Symfony\UX\LiveComponent\DefaultActionTrait;
use Symfony\UX\LiveComponent\LiveCollectionTrait;

#[AsLiveComponent]
final class AddRecipe extends AbstractController
{
    use DefaultActionTrait;
    use ComponentWithFormTrait;
    use LiveCollectionTrait;


    public function getImage()
    {
        $image = $this->getForm()->get('image')->getData();
        return $image;
    }

    protected function instantiateForm(): FormInterface
    {
        return $this->createForm(RecipeType::class);
    }
}
