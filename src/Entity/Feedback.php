<?php

namespace App\Entity;

use App\Entity\Prestation;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Link;
use ApiPlatform\Metadata\Post;
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

    #[Groups(['etablissement:read:public'])]
    #[ORM\Column]
    private array $notes = [];

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

    public function getNotes(): array
    {
        return $this->notes;
    }

    public function setNotes(array $notes): static
    {
        $this->notes = $notes;

        return $this;
    }
}
