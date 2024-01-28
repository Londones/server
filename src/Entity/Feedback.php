<?php

namespace App\Entity;

use App\Entity\Prestation;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Link;
use ApiPlatform\Metadata\Post;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Metadata\ApiResource;
use App\Repository\FeedbackRepository;
use ApiPlatform\Metadata\GetCollection;
use App\Entity\Traits\TimestampableTrait;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: FeedbackRepository::class)]
#[ApiResource(
    uriTemplate: '/prestations/{id}/feedbacks',
    operations: [new GetCollection],
    uriVariables: [
        'id' => new Link(toProperty: 'prestation', fromClass: Prestation::class)
    ]
)]

#[ApiResource(
    normalizationContext: ['groups' => ['feedback:read', 'etablissement:read:public']],
    denormalizationContext: ['groups' => ['feedback:write']],
    operations: [
        new GetCollection(),
        new Post(),
        new Get(),
    ]
)]
class Feedback
{
    use TimestampableTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[Groups(['feedback:read', 'feedback:write'])]
    #[ORM\ManyToOne(inversedBy: 'feedback')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $client = null;

    #[Groups(['feedback:read', 'feedback:write'])]
    #[ORM\ManyToOne(inversedBy: 'feedback')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Prestation $prestation = null;

    #[Groups(['feedback:read', 'feedback:write', 'etablissement:read:public'])]
    #[ORM\Column]
    private ?int $note_globale = null;

    #[ORM\OneToMany(mappedBy: 'feedback', targetEntity: Critere::class)]
    #[Groups(['etablissement:read:public'])]
    private Collection $criteres;

    public function __construct()
    {
        $this->criteres = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getClient(): ?User
    {
        return $this->client;
    }

    public function setClient(?User $client): static
    {
        $this->client = $client;

        return $this;
    }


    public function getPrestation(): ?Prestation
    {
        return $this->prestation;
    }

    public function setPrestation(?Prestation $prestation): static
    {
        $this->prestation = $prestation;

        return $this;
    }

    public function getNoteGlobale(): ?int
    {
        return $this->note_globale;
    }

    public function setNoteGlobale(int $note_globale): static
    {
        $this->note_globale = $note_globale;

        return $this;
    }

    /**
     * @return Collection<int, Critere>
     */
    public function getCriteres(): Collection
    {
        return $this->criteres;
    }

    public function addCritere(Critere $critere): static
    {
        if (!$this->criteres->contains($critere)) {
            $this->criteres->add($critere);
            $critere->setFeedback($this);
        }

        return $this;
    }

    public function removeCritere(Critere $critere): static
    {
        if ($this->criteres->removeElement($critere)) {
            // set the owning side to null (unless already changed)
            if ($critere->getFeedback() === $this) {
                $critere->setFeedback(null);
            }
        }

        return $this;
    }
}
