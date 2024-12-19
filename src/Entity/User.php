<?php

namespace App\Entity;

use App\Entity\Trait\BlameableTrait;
use App\Entity\Trait\DefaultTrait;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\HasLifecycleCallbacks]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_EMAIL', fields: ['email'])]
#[UniqueEntity(fields: ['email'], message: 'There is already an account with this email')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    use DefaultTrait;
    use TimestampableEntity;
    use BlameableTrait;

    #[ORM\Column(length: 180)]
    private ?string $email = null;

    /**
     * @var list<string> The user roles
     */
    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    /**
     * @var Collection<int, UserRecipeSaved>
     */
    #[ORM\OneToMany(targetEntity: UserRecipeSaved::class, mappedBy: 'user')]
    private Collection $userRecipeSaveds;

    /**
     * @var Collection<int, UserRecipeRating>
     */
    #[ORM\OneToMany(targetEntity: UserRecipeRating::class, mappedBy: 'user')]
    private Collection $userRecipeRatings;

    public function __construct()
    {
        $this->userRecipeSaveds = new ArrayCollection();
        $this->userRecipeRatings = new ArrayCollection();
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): static
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     *
     * @return list<string>
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    /**
     * @param list<string> $roles
     */
    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
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
            $userRecipeSaved->setUser($this);
        }

        return $this;
    }

    public function removeUserRecipeSaved(UserRecipeSaved $userRecipeSaved): static
    {
        if ($this->userRecipeSaveds->removeElement($userRecipeSaved)) {
            // set the owning side to null (unless already changed)
            if ($userRecipeSaved->getUser() === $this) {
                $userRecipeSaved->setUser(null);
            }
        }

        return $this;
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
            $userRecipeRating->setUser($this);
        }

        return $this;
    }

    public function removeUserRecipeRating(UserRecipeRating $userRecipeRating): static
    {
        if ($this->userRecipeRatings->removeElement($userRecipeRating)) {
            // set the owning side to null (unless already changed)
            if ($userRecipeRating->getUser() === $this) {
                $userRecipeRating->setUser(null);
            }
        }

        return $this;
    }
}
