<?php

namespace App\Twig\Components\Pages;

use App\Form\SearchFormType;
use App\Repository\RecipeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\ComponentToolsTrait;
use Symfony\UX\LiveComponent\ComponentWithFormTrait;
use Symfony\UX\LiveComponent\DefaultActionTrait;
use Symfony\UX\TwigComponent\Attribute\ExposeInTemplate;

#[AsLiveComponent]
final class SearchRecipes extends AbstractController
{
    use ComponentToolsTrait;
    use DefaultActionTrait;
    use ComponentWithFormTrait;

    public array $recipes;

    private const int PER_PAGE = 21;

    #[LiveProp]
    public int $page = 1;

    public function __construct(private readonly RecipeRepository $recipeRepository, private readonly RequestStack $requestStack)
    {
    }

    public function getRecipes(): array
    {
        $search = $this->getForm()->getData() ?? [];
        $recipeCollection = [];
        $recipes = $this->recipeRepository->search($search, (self::PER_PAGE * $this->page));
        foreach ($recipes as $i => $recipe) {
            $recipeCollection[] = [
                'id' => ($this->page - 1) * self::PER_PAGE + $i,
                'recipe' => $recipe,
            ];
        }

        return $recipeCollection;
    }

    protected function instantiateForm(): FormInterface
    {
        return $this->createForm(SearchFormType::class);
    }

    #[ExposeInTemplate('per_page')]
    public function getPerPage(): int
    {
        return self::PER_PAGE;
    }

    public function getAmount(): int
    {
        $search = $this->getForm()->getData() ?? [];
        $countRecipes = $this->recipeRepository->search($search, count: true);

        return count($countRecipes);
    }

    public function hasMore(): bool
    {
        $search = $this->getForm()->getData() ?? [];
        $countRecipes = $this->recipeRepository->search($search, count: true);

        return count($countRecipes) > ($this->page * self::PER_PAGE);
    }

    #[LiveAction]
    public function more(): void
    {
        ++$this->page;
    }

    #[LiveAction]
    public function clearFilter()
    {
        $session = $this->requestStack->getSession();
        $session->set('mainSearch', null);
    }

    private function getDataModelValue(): ?string
    {
        return 'on(input)|*';
    }
}
