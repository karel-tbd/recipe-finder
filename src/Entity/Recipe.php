<?php

namespace App\Entity;

use App\Entity\Trait\BlameableTrait;
use App\Entity\Trait\DefaultTrait;
use App\Enum\MealCountry;
use App\Enum\MealType;
use App\Enum\Publish;
use App\Repository\RecipeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints\NotBlank;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

#[ORM\Entity(repositoryClass: RecipeRepository::class)]
#[ORM\HasLifecycleCallbacks]
#[Vich\Uploadable]
class Recipe
{
    use DefaultTrait;
    use TimestampableEntity;
    use BlameableTrait;

    #[ORM\Column(length: 255)]
    #[NotBlank]
    private ?string $name = null;

    #[Vich\UploadableField(mapping: 'recipe_files', fileNameProperty: 'imageName', size: 'imageSize')]
    private ?File $image = null;

    #[ORM\Column(nullable: true)]
    private ?string $imageName = null;

    #[ORM\Column(nullable: true)]
    private ?int $imageSize = null;

    #[ORM\Column(type: Types::TEXT)]
    #[NotBlank]
    private ?string $description = null;

    #[ORM\Column]
    #[NotBlank]
    private ?float $time = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[NotBlank]
    private ?string $instructions = null;

    /**
     * @var Collection<int, RecipeIngredients>
     */
    #[ORM\OneToMany(targetEntity: RecipeIngredients::class, mappedBy: 'recipe', cascade: ['persist', 'remove'])]
    private Collection $recipeIngredients;

    #[ORM\Column]
    #[NotBlank]
    private ?int $people = null;

    #[ORM\Column(type: Types::SIMPLE_ARRAY, nullable: true, enumType: MealType::class)]
    #[NotBlank]
    private ?array $mealType = [];

    /**
     * @var Collection<int, UserRecipeSaved>
     */
    #[ORM\OneToMany(targetEntity: UserRecipeSaved::class, mappedBy: 'recipe', cascade: ['remove'])]
    private Collection $userRecipeSaveds;

    /**
     * @var Collection<int, UserRecipeRating>
     */
    #[ORM\OneToMany(targetEntity: UserRecipeRating::class, mappedBy: 'recipe')]
    private Collection $userRecipeRatings;

    #[ORM\Column(nullable: true, enumType: MealCountry::class)]
    private ?MealCountry $country = null;

    #[ORM\Column(enumType: Publish::class)]
    private ?Publish $status = null;

    #[ORM\Column]
    private ?bool $publish = null;

    public function __construct()
    {
        $this->recipeIngredients = new ArrayCollection();
        $this->userRecipeSaveds = new ArrayCollection();
        $this->userRecipeRatings = new ArrayCollection();
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getTime(): ?float
    {
        return $this->time;
    }

    public function setTime(?float $time): static
    {
        $this->time = $time;

        return $this;
    }

    public function setImage(?File $image = null): void
    {
        $this->image = $image;

        if ($image instanceof File) {

            $this->updatedAt = new \DateTimeImmutable();
        }
    }

    public function getImage(): ?File
    {
        return $this->image;
    }

    public function setImageName(?string $imageName): void
    {
        $this->imageName = $imageName;
    }

    public function getImageName(): ?string
    {
        return $this->imageName;
    }

    public function setImageSize(?int $imageSize): void
    {
        $this->imageSize = $imageSize;
    }

    public function getImageSize(): ?int
    {
        return $this->imageSize;
    }

    public function getInstructions(): ?string
    {
        return $this->instructions;
    }

    public function setInstructions(?string $instructions): static
    {
        $this->instructions = $instructions;

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
            $recipeIngredient->setRecipe($this);
        }

        return $this;
    }

    public function removeRecipeIngredient(RecipeIngredients $recipeIngredient): static
    {
        if ($this->recipeIngredients->removeElement($recipeIngredient)) {
            // set the owning side to null (unless already changed)
            if ($recipeIngredient->getRecipe() === $this) {
                $recipeIngredient->setRecipe(null);
            }
        }

        return $this;
    }

    public function getPeople(): ?int
    {
        return $this->people;
    }

    public function setPeople(?int $people): static
    {
        $this->people = $people;

        return $this;
    }

    /**
     * @return MealType[]
     */
    public function getMealType(): ?array
    {
        return $this->mealType;
    }

    public function setMealType(?array $mealType): static
    {
        $this->mealType = $mealType;

        return $this;
    }

    /**
     * @return Collection<int, UserRecipeSaved>
     */
    public function getUserRecipeSaveds(): Collection
    {
        return $this->userRecipeSaveds;
    }

    public function addUserRecipeSaved(UserRecipeSaved $userRecipeSaved): static
    {
        if (!$this->userRecipeSaveds->contains($userRecipeSaved)) {
            $this->userRecipeSaveds->add($userRecipeSaved);
            $userRecipeSaved->setRecipe($this);
        }

        return $this;
    }

    public function removeUserRecipeSaved(UserRecipeSaved $userRecipeSaved): static
    {
        if ($this->userRecipeSaveds->removeElement($userRecipeSaved)) {
            // set the owning side to null (unless already changed)
            if ($userRecipeSaved->getRecipe() === $this) {
                $userRecipeSaved->setRecipe(null);
            }
        }

        return $this;
    }

    public function getRating(): array
    {
        $totalScore = 0;
        $count = $this->getUserRecipeRatings()->count();
        $score = $this->getUserRecipeRatings()->toArray();

        if (!empty($score)) {
            foreach ($score as $rating) {
                $totalScore += $rating->getScore();
            }
            $totalScore = $totalScore / $count;
        }
        return [
            'score' => round($totalScore, 1),
            'count' => $count,
        ];
    }

    /**
     * @return Collection<int, UserRecipeRating>
     */
    public function getUserRecipeRatings(): Collection
    {
        return $this->userRecipeRatings;
    }

    public function addUserRecipeRating(UserRecipeRating $userRecipeRating): static
    {
        if (!$this->userRecipeRatings->contains($userRecipeRating)) {
            $this->userRecipeRatings->add($userRecipeRating);
            $userRecipeRating->setRecipe($this);
        }

        return $this;
    }

    public function removeUserRecipeRating(UserRecipeRating $userRecipeRating): static
    {
        if ($this->userRecipeRatings->removeElement($userRecipeRating)) {
            // set the owning side to null (unless already changed)
            if ($userRecipeRating->getRecipe() === $this) {
                $userRecipeRating->setRecipe(null);
            }
        }

        return $this;
    }

    public function getCountry(): ?MealCountry
    {
        return $this->country;
    }

    public function setCountry(?MealCountry $country): static
    {
        $this->country = $country;

        return $this;
    }

    public function getStatus(): ?Publish
    {
        return $this->status;
    }

    public function setStatus(Publish $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function isPublish(): ?bool
    {
        return $this->publish;
    }

    public function setPublish(bool $publish): static
    {
        $this->publish = $publish;

        return $this;
    }
}
