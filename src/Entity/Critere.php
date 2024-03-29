<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\CritereRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use ApiPlatform\Metadata\GetCollection;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Delete;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\MaxDepth;

#[ORM\Entity(repositoryClass: CritereRepository::class)]
#[ApiResource(
    security: "is_granted('ROLE_USER')",
    normalizationContext: ['groups' => ['critere:read', 'category:read', 'prestation:read']],
    denormalizationContext: ['groups' => ['critere:write']],
    operations: [
        new GetCollection(normalizationContext: ["enable_max_depth" => "true"]),
        new Post(
            security: "is_granted('ROLE_ADMIN') or is_granted('ROLE_PRESTATAIRE')"
        ),
        new Get(),
        new Patch(
            security: "is_granted('ROLE_ADMIN') or is_granted('ROLE_PRESTATAIRE')"
        ),
        new Delete(
            security: "is_granted('ROLE_ADMIN') or is_granted('ROLE_PRESTATAIRE')"
        ),
    ]
)]

class Critere
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[MaxDepth(1)]
    private ?int $id = null;

    #[Groups(['critere:read', 'critere:write', 'category:read', 'prestation:read'])]
    #[ORM\Column(length: 255)]
    #[MaxDepth(1)]
    private ?string $titre = null;

    #[Groups(['critere:read', 'critere:write'])]
    #[MaxDepth(1)]
    #[ORM\ManyToMany(targetEntity: Category::class, mappedBy: 'criteres')]
    private Collection $categories;

    #[Groups(['critere:read', 'critere:write'])]
    #[MaxDepth(1)]
    #[ORM\OneToMany(mappedBy: 'critere', targetEntity: Feedback::class)]
    private Collection $feedbacks;

    public function __construct()
    {
        $this->categories = new ArrayCollection();
        $this->feedbacks = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitre(): ?string
    {
        return $this->titre;
    }

    public function setTitre(string $titre): static
    {
        $this->titre = $titre;

        return $this;
    }
    /**
     * @return Collection<int, Category>
     */
    public function getCategories(): Collection
    {
        return $this->categories;
    }

    public function addCategory(Category $category): static
    {
        if (!$this->categories->contains($category)) {
            $this->categories->add($category);
            $category->addCritere($this);
        }

        return $this;
    }

    public function removeCategory(Category $category): static
    {
        if ($this->categories->removeElement($category)) {
            $category->removeCritere($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, Feedback>
     */
    public function getFeedbacks(): Collection
    {
        return $this->feedbacks;
    }

    public function addFeedback(Feedback $feedback): static
    {
        if (!$this->feedbacks->contains($feedback)) {
            $this->feedbacks->add($feedback);
            $feedback->setCritere($this);
        }

        return $this;
    }

    public function removeFeedback(Feedback $feedback): static
    {
        if ($this->feedbacks->removeElement($feedback)) {
            // set the owning side to null (unless already changed)
            if ($feedback->getCritere() === $this) {
                $feedback->setCritere(null);
            }
        }

        return $this;
    }
}
