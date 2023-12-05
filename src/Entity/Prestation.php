<?php

namespace App\Entity;

use App\Filter\CustomSearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Delete;
use App\Repository\PrestationRepository;
use App\Entity\Traits\TimestampableTrait;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use Symfony\Component\Serializer\Annotation\Groups;
use ApiPlatform\Doctrine\Common\Filter\SearchFilterInterface;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: PrestationRepository::class)]
#[ApiResource(
    normalizationContext: ['groups' => ['prestation:read', 'date:read', 'etablissement:read:public']],
    denormalizationContext: ['groups' => ['prestation:write', 'date:write']],
    operations: [
        new GetCollection(),
        new Post(),
        new Get(normalizationContext: ['groups' => ['etablissement:read:public']]),
        new Patch(),
        new Delete(),
    ]
)]
#[ApiFilter(SearchFilter::class, strategy: SearchFilterInterface::STRATEGY_PARTIAL, properties: ['employes'])]
#[ApiFilter(SearchFilter::class, strategy: SearchFilterInterface::STRATEGY_PARTIAL, properties: ['employes.nom'])]
class Prestation
{
    use TimestampableTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['etablissement:read:public'])]
    private ?int $id = null;

    #[ApiFilter(CustomSearchFilter::class)]
    #[Assert\Length(min: 5)]
    #[Groups(['prestation:read', 'prestation:write', 'etablissement:read:public'])]
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $titre = null;

    #[Groups(['prestation:read', 'prestation:write', 'etablissement:read:public'])]
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $duree = null;

    #[Groups(['prestation:read:is-logged', 'prestation:write', 'etablissement:read:public'])]
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $prix = null;

    #[Groups(['prestation:read', 'prestation:write', 'etablissement:read:public'])]
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $description = null;

    #[Groups(['etablissement:read:public'])]
    #[ORM\ManyToOne(inversedBy: 'prestations')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Category $category = null;

    #[ORM\ManyToOne(inversedBy: 'prestation')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Etablissement $etablissement = null;

    #[ORM\ManyToMany(targetEntity: Employe::class, mappedBy: 'prestation')]
    private Collection $employes;

    #[ORM\OneToMany(mappedBy: 'prestation', targetEntity: Reservation::class)]
    private Collection $reservations;

    #[Groups(['etablissement:read:public'])]
    #[ORM\OneToMany(mappedBy: 'prestation', targetEntity: Feedback::class)]
    private Collection $feedback;

    public function __construct()
    {
        $this->employes = new ArrayCollection();
        $this->reservations = new ArrayCollection();
        $this->feedback = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitre(): ?string
    {
        return $this->titre;
    }

    public function setTitre(?string $titre): static
    {
        $this->titre = $titre;

        return $this;
    }

    public function getDuree(): ?string
    {
        return $this->duree;
    }

    public function setDuree(?string $duree): static
    {
        $this->duree = $duree;

        return $this;
    }

    public function getPrix(): ?string
    {
        return $this->prix;
    }

    public function setPrix(?string $prix): static
    {
        $this->prix = $prix;

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

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): static
    {
        $this->category = $category;

        return $this;
    }

    public function getEtablissement(): ?Etablissement
    {
        return $this->etablissement;
    }

    public function setEtablissement(?Etablissement $etablissement): static
    {
        $this->etablissement = $etablissement;

        return $this;
    }

    /**
     * @return Collection<int, Employe>
     */
    public function getEmployes(): Collection
    {
        return $this->employes;
    }

    public function addEmploye(Employe $employe): static
    {
        if (!$this->employes->contains($employe)) {
            $this->employes->add($employe);
            $employe->addPrestation($this);
        }

        return $this;
    }

    public function removeEmploye(Employe $employe): static
    {
        if ($this->employes->removeElement($employe)) {
            $employe->removePrestation($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, Reservation>
     */
    public function getReservations(): Collection
    {
        return $this->reservations;
    }

    public function addReservation(Reservation $reservation): static
    {
        if (!$this->reservations->contains($reservation)) {
            $this->reservations->add($reservation);
            $reservation->setPrestation($this);
        }

        return $this;
    }

    public function removeReservation(Reservation $reservation): static
    {
        if ($this->reservations->removeElement($reservation)) {
            // set the owning side to null (unless already changed)
            if ($reservation->getPrestation() === $this) {
                $reservation->setPrestation(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Feedback>
     */
    public function getFeedback(): Collection
    {
        return $this->feedback;
    }

    public function addFeedback(Feedback $feedback): static
    {
        if (!$this->feedback->contains($feedback)) {
            $this->feedback->add($feedback);
            $feedback->setPrestation($this);
        }

        return $this;
    }

    public function removeFeedback(Feedback $feedback): static
    {
        if ($this->feedback->removeElement($feedback)) {
            // set the owning side to null (unless already changed)
            if ($feedback->getPrestation() === $this) {
                $feedback->setPrestation(null);
            }
        }

        return $this;
    }
}
