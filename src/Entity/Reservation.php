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
use Symfony\Component\Serializer\Annotation\Groups;
use ApiPlatform\Metadata\ApiFilter;
use App\Filter\MonthFilter;
use App\Filter\MonthStatusFilter;

#[ORM\Entity(repositoryClass: ReservationRepository::class)]
#[ORM\Table(name: '`reservation`')]
#[ApiResource(
    normalizationContext: ['groups' => ['reservation:read', 'date:read']],
    denormalizationContext: ['groups' => ['reservation:write', 'date:write']],
    operations: [
        new GetCollection(),
        new Post(),
        new Get(normalizationContext: ['groups' => 'reservation:read', 'user:read']),
        new Patch(denormalizationContext: ['groups'=> 'reservation:update']),
        new Delete(),
    ]
)]

#[ApiFilter(MonthFilter::class)]
#[ApiFilter(MonthStatusFilter::class)]

class Reservation
{
    // use TimestampableTrait;
    
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[Groups(['reservation:read', 'reservation:write'])]
    #[ORM\ManyToOne(inversedBy: 'reservationsClient')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $client = null;

    #[Groups(['reservation:read', 'reservation:write', 'user:read'])]
    #[ORM\ManyToOne(inversedBy: 'reservations')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Prestation $prestation = null;

    #[Groups(['reservation:read', 'reservation:write', 'user:read'])]
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
}
