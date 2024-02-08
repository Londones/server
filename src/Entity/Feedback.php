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
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\MaxDepth;


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
        new Get(normalizationContext:["enable_max_depth" => "true"]),
    ]
)]

class Feedback
{
    #[Groups(['feedback:read'])]
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[Groups(['feedback:read', 'feedback:write'])]
    #[MaxDepth(1)]
    #[ORM\ManyToOne(inversedBy: 'feedback')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $client = null;

    #[Groups(['feedback:read', 'feedback:write'])]
    #[MaxDepth(1)]
    #[ORM\ManyToOne(inversedBy: 'feedback')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Prestation $prestation = null;

    #[Groups(['feedback:read', 'feedback:write'])]
    #[ORM\ManyToOne(inversedBy: 'feedbacks')]
    #[MaxDepth(1)]
    private ?Critere $critere = null;

    #[Groups(['feedback:read', 'feedback:write'])]
    #[ORM\ManyToOne(inversedBy: 'feedbacks')]
    #[ORM\Column(nullable: true)]
    private ?int $note = null;


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

    public function getCritere(): ?Critere
    {
        return $this->critere;
    }

    public function setCritere(?Critere $critere): static
    {
        $this->critere = $critere;

        return $this;
    }

    public function getNote(): ?int
    {
        return $this->note;
    }

    public function setNote(?int $note): static
    {
        $this->note = $note;

        return $this;
    }

}
