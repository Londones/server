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
use PHPUnit\TextUI\XmlConfiguration\Group;

#[ORM\Entity(repositoryClass: ReservationRepository::class)]
#[ApiResource(
    normalizationContext: ['groups' => ['reservation:read', 'date:read']],
    denormalizationContext: ['groups' => ['reservation:write', 'date:write']],
    operations: [
        new GetCollection(),
        new Post(),
        new Get(normalizationContext: ['groups' => 'reservation:read']),
        new Patch(denormalizationContext: ['groups'=> 'reservation:update']),
        new Delete(),
    ]
)]
class Reservation
{
    use TimestampableTrait;
    
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[Group(['reservation:read'])]
    #[ORM\ManyToOne(inversedBy: 'reservationsClient')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $client = null;

    #[Group(['reservation:read', 'reservation:update'])]
    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $dateTime = null;

    #[Group(['reservation:read'])]
    #[ORM\ManyToOne(inversedBy: 'reservations')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Prestation $prestation = null;

    #[Group(['reservation:read'])]
    #[ORM\ManyToOne(inversedBy: 'reservationsEmploye')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Employe $employe = null;

    #[Group(['reservation:read', 'reservation:update'])]
    #[ORM\Column(length: 255)]
    private ?string $status = null;

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

    public function getDateTime(): ?\DateTimeInterface
    {
        return $this->dateTime;
    }

    public function setDateTime(\DateTimeInterface $dateTime): static
    {
        $this->dateTime = $dateTime;

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
}
