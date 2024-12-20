<?php

namespace App\Twig\Components\Pages;

use App\Form\SearchFormType;
use App\Repository\RecipeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Form\FormInterface;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\ComponentWithFormTrait;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent]
final class MyRecipes extends AbstractController
{
    use DefaultActionTrait;
    use ComponentWithFormTrait;

    #[LiveProp(writable: true)]
    public string $query = '';

    public function __construct(private readonly RecipeRepository $recipeRepository, private readonly Security $security)
    {
    }

    public function getRecipes(): array
    {
        $user = $this->security->getUser();
        $search = $this->getForm()->getData() ?? [];
        $recipes = $this->recipeRepository->saved($user, $search);
        return $recipes;
    }

    protected function instantiateForm(): FormInterface
    {
        return $this->createForm(SearchFormType::class);
    }

    private function getDataModelValue(): ?string
    {
        return 'on(input)|*';
    }
}
