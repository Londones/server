<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\IndisponibiliteRepository;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: IndisponibiliteRepository::class)]
#[ApiResource (
    security: "is_granted('ROLE_USER')",
    normalizationContext: ['groups' => ['indisponibilite:read', 'employe:read'], "enable_max_depth" => "true"],
    denormalizationContext: ['groups' => ['indisponibilite:write'], "enable_max_depth" => "true"],
    operations: [
        new GetCollection(
            security: "is_granted('ROLE_PRESTATAIRE')",
        ),
        new Post(
            security: "is_granted('ROLE_PRESTATAIRE')",
        ),
        new Get(
            security: "is_granted('ROLE_PRESTATAIRE') or object.getOwner() == user",
        ),
        new Delete(
            security: "is_granted('ROLE_PRESTATAIRE')"
        )
    ]
)]
class Indisponibilite
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['indisponibilite:read', 'employe:read'])]
    private ?int $id = null;

    #[Groups(['indisponibilite:read', 'indisponibilite:write'])]
    #[ORM\ManyToOne(inversedBy: 'indisponibilites')]
    private ?Employe $employe = null;

    #[Groups(['indisponibilite:read', 'employe:read', 'indisponibilite:write', ])]
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $creneau = null;

    #[Groups(['indisponibilite:read', 'employe:read', 'indisponibilite:write'])]
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $jour = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): static
    {
        $this->id = $id;

        return $this;
    }

    public function getEmploye(): ?Employe
    {
        return $this->employe;
    }

    public function setEmploye(?Employe $employe): static
    {
        $this->employe = $employe;

        return $this;
    }

    public function getCreneau(): ?string
    {
        return $this->creneau;
    }

    public function setCreneau(?string $creneau): static
    {
        $this->creneau = $creneau;

        return $this;
    }

    public function getJour(): ?string
    {
        return $this->jour;
    }

    public function setJour(?string $jour): static
    {
        $this->jour = $jour;

        return $this;
    }

    public function getOwner (): ?User
    {
        return $this->getEmploye()->getEtablissement()->getPrestataire();
    }
}
