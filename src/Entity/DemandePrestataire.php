<?php

namespace App\Entity;

use App\Repository\DemandePrestataireRepository;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Metadata\ApiResource;
use Symfony\Component\Serializer\Annotation\Groups;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Delete;
use App\Entity\Traits\TimestampableTrait;

#[ORM\Entity(repositoryClass: DemandePrestataireRepository::class)]
#[ApiResource(
    normalizationContext: ['groups' => 'demande:read'],
    denormalizationContext: ['groups' => 'demande:update'],
    operations: [
        new GetCollection(),
        new Post(),
        new Get(),
        new Patch(),
        new Delete(),
    ]
)]
class DemandePrestataire
{
    use TimestampableTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['demande:read'])]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'validationPrestataire')]
    #[Groups(['demande:read'])]
    private ?User $prestataire = null;

    #[ORM\Column]
    #[Groups(['demande:update'])]
    private ?string $statut = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPrestataire(): ?User
    {
        return $this->prestataire;
    }

    public function setPrestataire(?User $prestataire): static
    {
        $this->prestataire = $prestataire;

        return $this;
    }

    public function getStatut(): ?string
    {
        return $this->statut;
    }

    public function setStatut(?string $statut): static
    {
        $this->statut = $statut;

        return $this;
    }
}
