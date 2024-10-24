<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\UserRepository;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Doctrine\Common\Filter\SearchFilterInterface;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Delete;
use App\Entity\Traits\TimestampableTrait;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints\Length;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use App\Entity\Etablissement;
use Symfony\Component\Serializer\Annotation\MaxDepth;
use App\Filter\RoleFilter;
use App\Filter\MonthUserFilter;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
#[UniqueEntity(fields: ['email'], message: 'Un compte existe déjà avec cet email')]
#[Vich\Uploadable]
#[ApiResource(
    normalizationContext: ['groups' => ['user:read', 'date:read'], "enable_max_depth" => true],
    denormalizationContext: ['groups' => ['user:write', 'user:write:update', 'date:write'], "enable_max_depth" => true],
    operations: [
        new GetCollection(
            normalizationContext: ['groups' => ['user:read', 'date:read', 'etablissement:read']]
        ),
        new Post(
            security: "is_granted('ROLE_ADMIN')"
        ),
        new Get(
            normalizationContext: ['groups' => ['user:read', 'user:read:full', 'etablissement:read', 'demande:read', 'reservations:read'], "enable_max_depth" => true],
            security: "is_granted('ROLE_ADMIN') or object.getOwner() == user"
        ),
        new Patch(
            security: "is_granted('ROLE_ADMIN') or object.getOwner() == user"
        ),
        new Delete(
            security: "is_granted('ROLE_ADMIN') or object.getOwner() == user"
        ),
    ]
)]
#[ApiFilter(RoleFilter::class)]
#[ApiFilter(MonthUserFilter::class)]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    use TimestampableTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['user:read', 'etablissement:read'])]
    private ?int $id = null;

    #[Groups(['user:read', 'user:write:update', 'user:write', 'etablissement:create'])]
    #[ApiFilter(SearchFilter::class, strategy: SearchFilterInterface::STRATEGY_EXACT)]
    #[ORM\Column(length: 255, unique: true)]
    private ?string $email = null;

    #[Groups(['user:read', 'user:write:update', 'user:write', 'demande:read', 'etablissement:read', 'etablissement:create', 'reservation:read'])]
    #[Assert\Length(min: 2)]
    #[ORM\Column(length: 255)]
    private ?string $nom = null;

    #[Groups(['user:read', 'user:write:update', 'user:write', 'demande:read', 'etablissement:read', 'etablissement:create', 'reservation:read'])]
    #[Assert\Length(min: 2)]
    #[ORM\Column(length: 255)]
    private ?string $prenom = null;

    #[ORM\Column]
    private ?string $password = null;  // Pas de group pour masquer ce champ

    #[Length(min: 6)]
    #[Groups(['user:write', 'user:write:update', 'etablissement:create'])]
    private ?string $plainPassword = null;

    #[Groups(['user:read', 'user:write:update', 'user:write'])]
    #[ORM\Column]
    private array $roles = [];

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $imageName = null;

    #[Groups(['user:write:update'])]
    #[Vich\UploadableField(mapping: 'users_images', fileNameProperty: 'imageName')]
    #[Assert\Image(
        maxSize: '2M',
        mimeTypes: ['image/png', 'image/jpeg'],
        maxSizeMessage: 'Votre fichier fait {{ size }} et ne doit pas dépasser {{ limit }}',
        mimeTypesMessage: 'Format accepté : png/jpeg'
    )]
    private ?File $imageFile = null;

    #[Groups(['user:read', 'user:write'])]
    #[ORM\Column]
    private ?bool $emailVerified = false;

    #[ORM\OneToMany(mappedBy: 'prestataire', targetEntity: Etablissement::class)]
    private Collection $etablissement;

    #[Groups(['user:read', 'reservations:read'])]
    #[ORM\OneToMany(mappedBy: 'client', targetEntity: Reservation::class)]
    #[MaxDepth(1)]
    private Collection $reservationsClient;

    #[Groups(['user:read', 'feedback:read'])]
    #[MaxDepth(1)]
        #[ORM\OneToMany(mappedBy: 'client', targetEntity: Feedback::class)]
    private Collection $feedback;

    public function __construct()
    {
        $this->etablissement = new ArrayCollection();
        $this->reservationsClient = new ArrayCollection();
        $this->feedback = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(?string $nom): static
    {
        $this->nom = $nom;

        return $this;
    }

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(?string $prenom): static
    {
        $this->prenom = $prenom;

        return $this;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;
        return $this;
    }

    public function getPlainPassword(): ?string
    {
        return $this->plainPassword;
    }

    public function setPlainPassword(?string $plainPassword): User
    {
        $this->plainPassword = $plainPassword;
        $this->password = $plainPassword;

        return $this;
    }

    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    public function getRoles(): array
    {
        $roles = $this->roles;
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    public function getImageName(): ?string
    {
        return $this->imageName;
    }

    public function setImageName(?string $imageName): User
    {
        $this->imageName = $imageName;

        return $this;
    }

    public function getImageFile(): ?File
    {
        return $this->imageFile;
    }

    public function setImageFile(?File $imageFile = null): void
    {
        $this->imageFile = $imageFile;
    }

    public function isEmailVerified(): ?bool
    {
        return $this->emailVerified;
    }

    public function setEmailVerified(bool $emailVerified): static
    {
        $this->emailVerified = $emailVerified;

        return $this;
    }

    public function eraseCredentials(): void
    {
        $this->plainPassword = null;
    }

    public function getEtablissement(): Collection
    {
        return $this->etablissement;
    }

    public function addEtablissement(Etablissement $etablissement): static
    {
        if (!$this->etablissement->contains($etablissement)) {
            $this->etablissement->add($etablissement);
            $etablissement->setPrestataire($this);
        }

        return $this;
    }

    public function removeEtablissement(Etablissement $etablissement): static
    {
        if ($this->etablissement->removeElement($etablissement)) {
            if ($etablissement->getPrestataire() === $this) {
                $etablissement->setPrestataire(null);
            }
        }

        return $this;
    }

    public function getReservationsClient(): Collection
    {
        return $this->reservationsClient;
    }

    public function addReservationsClient(Reservation $reservationsClient): static
    {
        if (!$this->reservationsClient->contains($reservationsClient)) {
            $this->reservationsClient->add($reservationsClient);
            $reservationsClient->setClient($this);
        }

        return $this;
    }

    public function removeReservationsClient(Reservation $reservationsClient): static
    {
        if ($this->reservationsClient->removeElement($reservationsClient)) {
            if ($reservationsClient->getClient() === $this) {
                $reservationsClient->setClient(null);
            }
        }

        return $this;
    }

    public function getFeedback(): Collection
    {
        return $this->feedback;
    }

    public function addFeedback(Feedback $feedback): static
    {
        if (!$this->feedback->contains($feedback)) {
            $this->feedback->add($feedback);
            $feedback->setClient($this);
        }

        return $this;
    }

    public function removeFeedback(Feedback $feedback): static
    {
        if ($this->feedback->removeElement($feedback)) {
            if ($feedback->getClient() === $this) {
                $feedback->setClient(null);
            }
        }

        return $this;
    }

    public function getOwner(): ?UserInterface
    {
        return $this;
    }
}
