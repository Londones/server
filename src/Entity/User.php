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
use ApiPlatform\Metadata\Post;
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

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
#[UniqueEntity(fields: ['email'], message: 'Un compte existe déjà avec cet email')]
#[Vich\Uploadable]
#[ApiResource(
    normalizationContext: ['groups' => ['user:read', 'date:read']],
    denormalizationContext: ['groups' => ['user:write', 'date:write']],
    operations: [
        new GetCollection(),
        new Post(),
        new Get(normalizationContext: ['groups' => ['user:read', 'user:read:full']]),
        new Patch(denormalizationContext: ['groups' => ['user:write:update']]),
    ]
)]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    use TimestampableTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[Groups(['user:read', 'user:write'])]
    #[ORM\Column(length: 255, unique: true)]
    private ?string $email = null;

    #[Groups(['user:read', 'user:write:update', 'user:write'])]
    #[Assert\Length(min: 2)]
    #[ORM\Column(length: 255)]
    private ?string $nom = null;

    #[Groups(['user:read', 'user:write:update', 'user:write'])]
    #[Assert\Length(min: 2)]
    #[ORM\Column(length: 255)]
    private ?string $prenom = null;

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    #[Groups(['user:write', 'user:write:update'])]
    #[Length(min: 6)]
    private ?string $plainPassword = null;

    #[Groups(['user:read:full'])]
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

    #[ORM\Column]
    private ?bool $emailVerified = false;

    #[ORM\OneToMany(mappedBy: 'prestataire', targetEntity: Etablissement::class)]
    private Collection $etablissement;

    #[ORM\OneToMany(mappedBy: 'client', targetEntity: Reservation::class)]
    private Collection $reservationsClient;

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

    public function getPlainPassword(): string
    {
        return $this->plainPassword;
    }

    public function setPlainPassword(string $plainPassword): void
    {
        $this->plainPassword = $plainPassword;
        $this->password = $plainPassword;
    }
    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
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

    /**
     * @param string|null $imageName
     * @return User
     */
    public function setImageName(?string $imageName): User
    {
        $this->imageName = $imageName;

        return $this;
    }

    public function getImageFile(): ?File
    {
        return $this->imageFile;
    }

    /**
     * @param mixed $imageFile
     * @return User
     */
    public function setImageFile(?File $imageFile = null): void
    {
        $this->imageFile = $imageFile;

        if (null !== $imageFile) {
            $this->updatedAt = new \DateTime();
        }
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

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        $this->plainPassword = null;
    }

    /**
     * @return Collection<int, Etablissement>
     */
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
            // set the owning side to null (unless already changed)
            if ($etablissement->getPrestataire() === $this) {
                $etablissement->setPrestataire(null);
            }
        }

        return $this;
    }

    public function hasRole($role)
    {
        return in_array($role, $this->roles);
    }

    /**
     * @return Collection<int, Reservation>
     */
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
            // set the owning side to null (unless already changed)
            if ($reservationsClient->getClient() === $this) {
                $reservationsClient->setClient(null);
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
            $feedback->setClient($this);
        }

        return $this;
    }

    public function removeFeedback(Feedback $feedback): static
    {
        if ($this->feedback->removeElement($feedback)) {
            // set the owning side to null (unless already changed)
            if ($feedback->getClient() === $this) {
                $feedback->setClient(null);
            }
        }

        return $this;
    }
}
