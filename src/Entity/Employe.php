<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use App\Repository\EmployeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\Traits\TimestampableTrait;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Serializer\Annotation\Groups;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: EmployeRepository::class)]
#[Vich\Uploadable]
#[ApiResource(
    normalizationContext: ['groups' => ['employe:read', 'date:read', 'etablissement:read:public']],
    denormalizationContext: ['groups' => ['employe:write', 'date:write']],
    operations: [
        new GetCollection(),
        new Post(),
        new Get(normalizationContext: ['groups' => ['employe:read', 'employe:read:full', 'etablissement:read:public']]),
        new Patch(denormalizationContext: ['groups' => ['employe:update']]),
    ]
)]
class Employe
{
    use TimestampableTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[Groups(['employe:read', 'employe:update', 'etablissement:read:public'])]
    #[ORM\Column(length: 255)]
    private ?string $nom = null;

    #[Groups(['employe:read', 'employe:update', 'etablissement:read:public'])]
    #[ORM\Column(length: 255)]
    private ?string $prenom = null;

    #[Groups(['employe:read', 'employe:update'])]
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $horraires_service = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $imageName = null;

    #[Groups(['employe:update', 'etablissement:read:public'])]
    #[Vich\UploadableField(mapping: 'employes_images', fileNameProperty: 'imageName')]
    #[Assert\Image(
        maxSize: '2M',
        mimeTypes: ['image/png', 'image/jpeg'],
        maxSizeMessage: 'Votre fichier fait {{ size }} et ne doit pas dépasser {{ limit }}',
        mimeTypesMessage: 'Format accepté : png/jpeg'
    )]
    private ?File $imageFile = null;

    #[Groups(['employe:read', 'employe:update', 'etablissement:read:public'])]
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $description = null;

    #[ORM\ManyToOne(inversedBy: 'employes')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Etablissement $etablissement = null;

    #[ORM\ManyToMany(targetEntity: Prestation::class, inversedBy: 'employes')]
    private Collection $prestation;

    #[ORM\OneToMany(mappedBy: 'employe', targetEntity: Prestation::class)]
    private Collection $prestations;

    #[ORM\OneToMany(mappedBy: 'employe', targetEntity: Reservation::class)]
    private Collection $reservationsEmploye;


    public function __construct()
    {
        $this->prestation = new ArrayCollection();
        $this->prestations = new ArrayCollection();
        $this->reservationsEmploye = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getHorrairesService(): ?string
    {
        return $this->horraires_service;
    }

    public function setHorrairesService(?string $horraires_service): static
    {
        $this->horraires_service = $horraires_service;

        return $this;
    }

    public function getImageName(): ?string
    {
        return $this->imageName;
    }

    public function setImageName(?string $imageName): static
    {
        $this->imageName = $imageName;

        return $this;
    }

    public function getImageFile(): ?File
    {
        return $this->imageFile;
    }

    public function setImageFile(?File $imageFile): static
    {
        $this->imageFile = $imageFile;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

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

    /**
     * @return Collection<int, Prestation>
     */
    public function getPrestation(): Collection
    {
        return $this->prestation;
    }

    public function addPrestation(Prestation $prestation): static
    {
        if (!$this->prestation->contains($prestation)) {
            $this->prestation->add($prestation);
        }

        return $this;
    }

    public function removePrestation(Prestation $prestation): static
    {
        $this->prestation->removeElement($prestation);

        return $this;
    }

    /**
     * @return Collection<int, Prestation>
     */
    public function getPrestations(): Collection
    {
        return $this->prestations;
    }

    /**
     * @return Collection<int, Reservation>
     */
    public function getReservationsEmploye(): Collection
    {
        return $this->reservationsEmploye;
    }

    public function addReservationsEmploye(Reservation $reservationsEmploye): static
    {
        if (!$this->reservationsEmploye->contains($reservationsEmploye)) {
            $this->reservationsEmploye->add($reservationsEmploye);
            $reservationsEmploye->setEmploye($this);
        }

        return $this;
    }

    public function removeReservationsEmploye(Reservation $reservationsEmploye): static
    {
        if ($this->reservationsEmploye->removeElement($reservationsEmploye)) {
            // set the owning side to null (unless already changed)
            if ($reservationsEmploye->getEmploye() === $this) {
                $reservationsEmploye->setEmploye(null);
            }
        }

        return $this;
    }
}
