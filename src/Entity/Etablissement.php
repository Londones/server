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
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints as Assert;

#[Vich\Uploadable]
#[ORM\Entity(repositoryClass: EtablissementRepository::class)]
#[ApiResource(
    normalizationContext: ['groups' => 'etablissement:read'],
    denormalizationContext: ['groups' => 'etablissement:write'],
    operations: [
        new GetCollection(normalizationContext: ['groups' => ['etablissement:read']]),
        new GetCollection(
            uriTemplate: '/etablissementsList',
            normalizationContext: ['groups' => ['etablissement:read:list']]
        ),
        new Post(
            denormalizationContext: ['groups' => ['etablissement:create']],
            inputFormats: ['multipart' => ['multipart/form-data']]
        ),
        new Get(normalizationContext: ['groups' => ['etablissement:read', 'etablissement:read:public']]),
        new Get(
            uriTemplate: '/etablissementPublic/{id}',
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

    #[Groups(['etablissement:read', 'etablissement:update', 'etablissement:read:public', 'etablissement:create'])]
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $adresse = null;

    #[Groups(['etablissement:read', 'etablissement:update'])]
    #[ORM\Column]
    private ?bool $validation = false;

    #[Groups(['etablissement:read', 'etablissement:update', 'etablissement:read:public'])]
    #[ORM\Column(length: 255, name: 'horaires_ouverture')]
    private ?string $horairesOuverture = null;

    #[Groups(['etablissement:read', 'etablissement:create'])]
    #[ORM\ManyToOne(inversedBy: 'etablissement', cascade: ['persist'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $prestataire = null;

    #[ORM\OneToMany(mappedBy: 'etablissement', targetEntity: Prestation::class, cascade: ['persist'])]
    #[Groups(['etablissement:read:public', 'etablissement:create'])]
    private Collection $prestation;

    #[ORM\OneToMany(mappedBy: 'etablissement', targetEntity: Employe::class)]
    #[Groups(['etablissement:read:public'])]
    private Collection $employes;

    #[ORM\OneToMany(mappedBy: 'etablissement', targetEntity: ImageEtablissement::class)]
    #[Groups(['etablissement:read:public'])]
    private ?Collection $imageEtablissements = null;

    #[Vich\UploadableField(mapping: 'etablissement', fileNameProperty: 'kbisName')]
    #[Groups(['etablissement:read', 'etablissement:create'])]
    // #[Assert\File(maxSize: '2M', mimeTypes: ['application/pdf'])]
    private ?File $kbisFile = null;

    #[Groups(['etablissement:read'])]
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $kbisName = null;

    #[Groups(['etablissement:read', 'etablissement:create'])]
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $latitude = null;

    #[Groups(['etablissement:read', 'etablissement:create'])]
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $longitude = null;

    #[Groups(['etablissement:read', 'etablissement:create'])]
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $ville = null;

    #[Groups(['etablissement:read', 'etablissement:create'])]
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

    public function isValidation(): ?bool
    {
        return $this->validation;
    }

    public function setValidation(?bool $validation): static
    {
        $this->validation = $validation;

        return $this;
    }

    public function getHorairesOuverture(): ?string
    {
        return $this->horairesOuverture;
    }

    public function setHorairesOuverture(?string $horairesOuverture): static
    {
        $this->horairesOuverture = $horairesOuverture;

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

    public function getLatitude(): ?string
    {
        return $this->latitude;
    }

    public function setLatitude(string $latitude): static
    {
        $this->latitude = $latitude;

        return $this;
    }

    public function getLongitude(): ?string
    {
        return $this->longitude;
    }

    public function setLongitude(string $longitude): static
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

    public function getKbisFile(): ?File
    {
        return $this->kbisFile;
    }

    public function setKbisFile(?File $kbisFile): static
    {
        $this->kbisFile = $kbisFile;

        return $this;
    }

    public function getKbisName(): ?string
    {
        return $this->kbisName;
    }

    public function setKbisName(?string $kbisName): static
    {
        $this->kbisName = $kbisName;

        return $this;
    }

}
