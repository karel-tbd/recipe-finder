<?php

namespace App\Entity;

use App\Repository\IngredientsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: IngredientsRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Ingredients
{

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(nullable: true)]
    private ?float $price = null;

    /**
     * @var Collection<int, RecipeIngredients>
     */
    #[ORM\OneToMany(targetEntity: RecipeIngredients::class, mappedBy: 'ingredient')]
    private Collection $recipeIngredients;

    #[ORM\Column(nullable: true)]
    private ?int $calories = null;

    /**
     * @var Collection<int, FoodGroup>
     */
    #[ORM\ManyToMany(targetEntity: FoodGroup::class, inversedBy: 'ingredients')]
    private Collection $foodgroup;

    public function __construct()
    {
        $this->recipeIngredients = new ArrayCollection();
        $this->foodgroup = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(float $price): static
    {
        $this->price = $price;

        return $this;
    }

    /**
     * @return Collection<int, RecipeIngredients>
     */
    public function getRecipeIngredients(): Collection
    {
        return $this->recipeIngredients;
    }

    public function addRecipeIngredient(RecipeIngredients $recipeIngredient): static
    {
        if (!$this->recipeIngredients->contains($recipeIngredient)) {
            $this->recipeIngredients->add($recipeIngredient);
            $recipeIngredient->setIngredient($this);
        }

        return $this;
    }

    public function removeRecipeIngredient(RecipeIngredients $recipeIngredient): static
    {
        if ($this->recipeIngredients->removeElement($recipeIngredient)) {
            // set the owning side to null (unless already changed)
            if ($recipeIngredient->getIngredient() === $this) {
                $recipeIngredient->setIngredient(null);
            }
        }

        return $this;
    }

    public function getCalories(): ?int
    {
        return $this->calories;
    }

    public function setCalories(?int $calories): static
    {
        $this->calories = $calories;

        return $this;
    }

    /**
     * @return Collection<int, FoodGroup>
     */
    public function getFoodgroup(): Collection
    {
        return $this->foodgroup;
    }

    public function addFoodgroup(FoodGroup $foodgroup): static
    {
        if (!$this->foodgroup->contains($foodgroup)) {
            $this->foodgroup->add($foodgroup);
        }

        return $this;
    }

    public function removeFoodgroup(FoodGroup $foodgroup): static
    {
        $this->foodgroup->removeElement($foodgroup);

        return $this;
    }

}
