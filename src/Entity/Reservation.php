<?php

namespace App\Entity;

use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Post;
use Doctrine\DBAL\Types\Types;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Delete;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use App\Entity\Traits\TimestampableTrait;
use App\Repository\ReservationRepository;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\MaxDepth;
use ApiPlatform\Metadata\ApiFilter;
use App\Filter\MonthFilter;
use App\Filter\MonthStatusFilter;
use ApiPlatform\Metadata\Link;

#[ORM\Entity(repositoryClass: ReservationRepository::class)]
#[ORM\Table(name: '`reservation`')]
#[ApiResource(
    security: "is_granted('ROLE_USER')",
    normalizationContext: ['groups' => ['reservation:read', 'date:read']],
    denormalizationContext: ['groups' => ['reservation:write', 'date:write']],
    operations: [
        new GetCollection(
            security: "is_granted('ROLE_ADMIN')"
        ),
        new GetCollection(
            uriTemplate: '/etablissements/{id}/reservations',
            uriVariables: [
                'id' => new Link(fromClass: Etablissement::class, fromProperty: 'id', toProperty: 'etablissement')
            ],
            normalizationContext: ['groups' => ['reservation:read']],
            security: "is_granted('ROLE_PRESTATAIRE') and object.getPrestataire() == user or is_granted('ROLE_ADMIN')"
        ),
        new Post(
            security: "is_granted('ROLE_ADMIN') or is_granted('ROLE_USER') or is_granted('ROLE_PRESTATAIRE')",
            normalizationContext: ['groups' => 'reservation:read', 'user:read']
        ),
        new Get(
            normalizationContext: ['groups' => 'reservation:read', 'user:read'],
            security: "object.getOwner() == user or is_granted('ROLE_PRESTATAIRE') and object.getPrestataire() == user or is_granted('ROLE_ADMIN')"
        ),
        new Patch(
            denormalizationContext: ['groups' => 'reservation:update'],
            security: "object.getOwner() == user or is_granted('ROLE_PRESTATAIRE') and object.getPrestataire() == user or is_granted('ROLE_ADMIN')"
        ),
        new Delete(
            security: "object.getOwner() == user or is_granted('ROLE_PRESTATAIRE') and object.getPrestataire() == user or is_granted('ROLE_ADMIN')"
        ),
    ]
)]
#[ApiFilter(MonthFilter::class)]
#[ApiFilter(MonthStatusFilter::class)]
class Reservation
{
    #[Groups(['reservation:read', 'prestation:read'])]
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[Groups(['reservation:read', 'reservation:write'])]
    #[MaxDepth(1)]
    #[ORM\ManyToOne(inversedBy: 'reservationsClient')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $client = null;

    #[Groups(['reservation:read', 'reservation:write', 'user:read'])]
    #[MaxDepth(1)]
    #[ORM\ManyToOne(inversedBy: 'reservations')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Prestation $prestation = null;

    #[Groups(['reservation:read', 'reservation:write', 'user:read'])]
    #[MaxDepth(1)]
    #[ORM\ManyToOne(inversedBy: 'reservationsEmploye')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Employe $employe = null;

    #[Groups(['reservation:read', 'reservation:update', 'reservation:write', 'user:read'])]
    #[ORM\Column(length: 255)]
    private ?string $status = null;

    #[Groups(['reservation:read', 'reservation:update', 'reservation:write', 'user:read'])]
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $creneau = null;

    #[Groups(['reservation:read', 'reservation:update', 'reservation:write', 'user:read'])]
    #[ORM\Column(nullable: true)]
    private ?int $duree = null;

    #[Groups(['reservation:read', 'reservation:update', 'reservation:write', 'user:read'])]
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $jour = null;

    #[Groups(['reservation:read'])]
    #[MaxDepth(1)]
    #[ORM\ManyToOne(inversedBy: 'reservations')]
    private ?Etablissement $etablissement = null;

    // Méthode pour obtenir l'ID
    #[Groups(['reservation:read'])]
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

    public function getEmploye(): ?Employe
    {
        return $this->employe;
    }

    public function setEmploye(?Employe $employe): static
    {
        $this->employe = $employe;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): static
    {
        $this->status = $status;

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

    public function getDuree(): ?int
    {
        return $this->duree;
    }

    public function setDuree(?int $duree): static
    {
        $this->duree = $duree;

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

    public function getEtablissement(): ?Etablissement
    {
        return $this->etablissement;
    }

    public function setEtablissement(?Etablissement $etablissement): static
    {
        $this->etablissement = $etablissement;

        return $this;
    }

    public function getPrestataire()
    {
        return $this->getEtablissement()->getPrestataire();
    }

    public function getOwner()
    {
        return $this->client;
    }
}
