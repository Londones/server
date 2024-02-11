<?php

namespace App\Entity;

use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Delete;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use App\Entity\Traits\TimestampableTrait;
use App\Repository\EtablissementRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use Symfony\Component\Serializer\Annotation\Groups;
use ApiPlatform\Doctrine\Common\Filter\SearchFilterInterface;

#[ORM\Entity(repositoryClass: EtablissementRepository::class)]
#[ApiResource(
    normalizationContext: ['groups' => 'etablissement:read'],
    denormalizationContext: ['groups' => 'etablissement:write'],
    operations: [
        new GetCollection(normalizationContext: ['groups' => ['etablissement:read']]),
        new GetCollection(
            uriTemplate: '/public/etablissementsList',
            normalizationContext: ['groups' => ['etablissement:read:list']]
        ),
        new Post(denormalizationContext: ['groups' => ['etablissement:update', 'etablissement:create']]),
        new Get(normalizationContext: ['groups' => ['etablissement:read', 'etablissement:read:public']]),
        new Get(
            uriTemplate: '/public/etablissementPublic/{id}',
            normalizationContext: ['groups' => ['etablissement:read:public']]
        ),
        new Patch(denormalizationContext: ['groups' => ['etablissement:update']]),
        new Delete(),
    ]
)]
class Etablissement
{
    use TimestampableTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['etablissement:read'])]
    private ?int $id = null;

    #[ApiFilter(SearchFilter::class, strategy: SearchFilterInterface::STRATEGY_EXACT)]
    #[Groups(['etablissement:read', 'etablissement:update', 'etablissement:read:public'])]
    #[ORM\Column(length: 255)]
    private ?string $nom = null;

    #[Groups(['etablissement:read', 'etablissement:update', 'etablissement:read:public'])]
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $adresse = null;

    #[Groups(['etablissement:read'])]
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $kbis = null;

    #[Groups(['etablissement:read', 'etablissement:update'])]
    #[ORM\Column]
    private ?bool $validation = false;

    #[Groups(['etablissement:read', 'etablissement:update', 'etablissement:read:public'])]
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $jours_ouverture = null;

    #[Groups(['etablissement:read', 'etablissement:update', 'etablissement:read:public'])]
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $horraires_ouverture = null;

    #[Groups(['etablissement:read', 'etablissement:create'])]
    #[ORM\ManyToOne(inversedBy: 'etablissement')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $prestataire = null;

    #[ORM\OneToMany(mappedBy: 'etablissement', targetEntity: Prestation::class)]
    #[Groups(['etablissement:read:public'])]
    private Collection $prestation;

    #[ORM\OneToMany(mappedBy: 'etablissement', targetEntity: Employe::class)]
    #[Groups(['etablissement:read:public'])]
    private Collection $employes;

    #[ORM\OneToMany(mappedBy: 'etablissement', targetEntity: ImageEtablissement::class)]
    #[Groups(['etablissement:read:public'])]
    private ?Collection $imageEtablissements = null;

    #[ORM\Column(nullable: true)]
    private ?float $latitude = null;

    #[ORM\Column(nullable: true)]
    private ?float $longitude = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $ville = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $codePostal = null;

    public function __construct()
    {
        $this->prestation = new ArrayCollection();
        $this->employes = new ArrayCollection();
        $this->imageEtablissements = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): static
    {
        $this->nom = $nom;

        return $this;
    }

    public function getAdresse(): ?string
    {
        return $this->adresse;
    }

    public function setAdresse(?string $adresse): static
    {
        $this->adresse = $adresse;

        return $this;
    }

    public function getKbis(): ?string
    {
        return $this->kbis;
    }

    public function setKbis(?string $kbis): static
    {
        $this->kbis = $kbis;

        return $this;
    }

    public function isValidation(): ?bool
    {
        return $this->validation;
    }

    public function setValidation(?bool $validation): static
    {
        $this->validation = $validation;

        return $this;
    }

    public function getJoursOuverture(): ?string
    {
        return $this->jours_ouverture;
    }

    public function setJoursOuverture(?string $jours_ouverture): static
    {
        $this->jours_ouverture = $jours_ouverture;

        return $this;
    }

    public function getHorrairesOuverture(): ?string
    {
        return $this->horraires_ouverture;
    }

    public function setHorrairesOuverture(?string $horraires_ouverture): static
    {
        $this->horraires_ouverture = $horraires_ouverture;

        return $this;
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
            $prestation->setEtablissement($this);
        }

        return $this;
    }

    public function removePrestation(Prestation $prestation): static
    {
        if ($this->prestation->removeElement($prestation)) {
            // set the owning side to null (unless already changed)
            if ($prestation->getEtablissement() === $this) {
                $prestation->setEtablissement(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Employe>
     */
    public function getEmployes(): Collection
    {
        return $this->employes;
    }

    public function addEmploye(Employe $employe): static
    {
        if (!$this->employes->contains($employe)) {
            $this->employes->add($employe);
            $employe->setEtablissement($this);
        }

        return $this;
    }

    public function removeEmploye(Employe $employe): static
    {
        if ($this->employes->removeElement($employe)) {
            // set the owning side to null (unless already changed)
            if ($employe->getEtablissement() === $this) {
                $employe->setEtablissement(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, ImageEtablissement>
     */
    public function getImageEtablissements(): Collection
    {
        return $this->imageEtablissements;
    }

    public function addImageEtablissement(ImageEtablissement $imageEtablissement): static
    {
        if (!$this->imageEtablissements->contains($imageEtablissement)) {
            $this->imageEtablissements->add($imageEtablissement);
            $imageEtablissement->setEtablissement($this);
        }

        return $this;
    }

    public function removeImageEtablissement(ImageEtablissement $imageEtablissement): static
    {
        if ($this->imageEtablissements->removeElement($imageEtablissement)) {
            // set the owning side to null (unless already changed)
            if ($imageEtablissement->getEtablissement() === $this) {
                $imageEtablissement->setEtablissement(null);
            }
        }

        return $this;
    }

    public function getLatitude(): ?float
    {
        return $this->latitude;
    }

    public function setLatitude(float $latitude): static
    {
        $this->latitude = $latitude;

        return $this;
    }

    public function getLongitude(): ?float
    {
        return $this->longitude;
    }

    public function setLongitude(float $longitude): static
    {
        $this->longitude = $longitude;

        return $this;
    }

    public function getVille(): ?string
    {
        return $this->ville;
    }

    public function setVille(string $ville): static
    {
        $this->ville = $ville;

        return $this;
    }

    public function getCodePostal(): ?string
    {
        return $this->codePostal;
    }

    public function setCodePostal(string $codePostal): static
    {
        $this->codePostal = $codePostal;

        return $this;
    }
}
