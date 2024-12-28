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
        $limit = 21;
        $page = $this->requestStack->getCurrentRequest()->query->get('page', 1);
        $search = $this->getForm()->getData() ?? [];

        return $this->recipeRepository->search($search, $limit, $page);
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
