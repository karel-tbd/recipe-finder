<?php

namespace App\Twig\Components\Pages;

use App\Form\SearchFormType;
use App\Repository\RecipeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\ComponentWithFormTrait;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent]
final class SearchRecipes extends AbstractController
{
    use DefaultActionTrait;
    use ComponentWithFormTrait;

    #[LiveProp(writable: true)]
    public string $query = '';

    public array $recipes;

    public function __construct(private readonly RecipeRepository $recipeRepository, private readonly RequestStack $requestStack)
    {
    }

    public function getRecipes(): array
    {
        $search = $this->getForm()->getData() ?? [];
        $page = $this->requestStack->getCurrentRequest()->query->get('page', 1);
        $limit = 21;

        return $this->recipeRepository->search($limit, $page, $search);
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
