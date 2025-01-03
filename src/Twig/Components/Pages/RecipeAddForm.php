<?php

namespace App\Twig\Components\Pages;

use App\Entity\Recipe as RecipeEntity;
use App\Form\RecipeAddType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\ComponentToolsTrait;
use Symfony\UX\LiveComponent\DefaultActionTrait;
use Symfony\UX\LiveComponent\LiveCollectionTrait;

#[AsLiveComponent]
final class RecipeAddForm extends AbstractController
{
    use DefaultActionTrait;
    use LiveCollectionTrait;
    use ComponentToolsTrait;

    public ?string $id = null;

    #[LiveProp(fieldName: 'formData')]
    public ?RecipeEntity $recipe = null;

    #[LiveProp]
    public int $step;

    #[LiveAction]
    public function next(): void
    {
        $this->submitForm();

        $values = $this->formValues;

        unset($values['_token']);

        $this->emit('next', [
            'values' => $values,
        ]);
    }

    #[LiveAction]
    public function prev(): void
    {
        $this->emit('prev');
    }

    protected function instantiateForm(): FormInterface
    {
        return $this->createForm(RecipeAddType::class, $this->recipe, ['step' => $this->step]);
    }
}
