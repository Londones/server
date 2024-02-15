<?php

namespace App\DataFixtures;

use App\Entity\Etablissement;
use App\Entity\ImageEtablissement;
use App\Entity\Employe;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Faker\Factory;
use App\Service\FileUploader;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Filesystem\Filesystem;


class EtablissementFixtures extends Fixture implements DependentFixtureInterface
{
    private $fileUploader;
    private $filesystem;

    public function __construct(FileUploader $fileUploader, Filesystem $filesystem)
    {
        $this->fileUploader = $fileUploader;

        $this->filesystem = $filesystem;
    }

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create("fr_FR");

        $adresses = [
            "20 Rue Santerre, 75012 Paris",
            "1 Avenue de la République, 75011 Paris",
            "118 Bd Diderot, 75012 Paris",
            "1 Rue de la Convention, 75015 Paris",
            "1 Rue Commines, 75003 Paris",
            "3 Bd des Capucines, 75002 Paris",
            "12 Rue Arthur Groussier, 75010 Paris",
            "14 Rue du Rocher, 75008 Paris",
            "24 Rue Collange, 92300 Levallois-Perret",
            "40 Av. de Saint-Ouen, 75018 Paris",
            "28 Rue Myrha, 75018 Paris"
        ];

        $villes = [
            "Paris",
            "Paris",
            "Paris",
            "Paris",
            "Paris",
            "Paris",
            "Paris",
            "Paris",
            "Levallois-Perret",
            "Paris",
            "Paris"
        ];

        $codesPostaux = [
            "75012",
            "75011",
            "75012",
            "75015",
            "75003",
            "75002",
            "75010",
            "75008",
            "92300",
            "75018",
            "75018"
        ];

        $lats = [
            48.8428611,
            48.8671134,
            48.8472264,
            48.8459647,
            48.8616884,
            48.8706538,
            48.8719157,
            48.8761196,
            48.8997573,
            48.8897734,
            48.887389
        ];

        $lngs = [
            2.3996666,
            2.3660679,
            2.3872668,
            2.2775933,
            2.3648706,
            2.3336093,
            2.3707818,
            2.3228499,
            2.2875812,
            2.3266478,
            2.3541197
        ];

        $noms = [
            "Le Jardin des Sens",
            "Café Douceur",
            "La Maison des Câlins",
            "Ami tendre Salon",
            "Doux Rendez-vous",
            "Le Coin des Sourires",
            "Oasis d'Amour",
            "L'Étreinte Chaleureuse",
            "Café Tendresse",
            "La Galerie des Cœurs"
        ];

        $prenomsEmploye = [
            "Jean",
            "Pierre",
            "Paul",
            "Jacques",
            "Marie",
            "Julie",
            "Lucie",
            "Jeanne",
            "Pierre",
            "Paul",
        ];

        $nomsEmploye = [
            "Dupont",
            "Durand",
            "Martin",
            "Bernard",
            "Dubois",
            "Thomas",
            "Robert",
            "Richard",
            "Petit",
            "Moreau",
        ];

        // create random etablissements
        for ($i = 0; $i < count($noms); $i++) {
            $etablissement = new Etablissement;
            $etablissement->setNom($noms[$i]);
            $etablissement->setAdresse($adresses[$i]);
            $etablissement->setValidation(true);
            $etablissement->setPrestataire($this->getReference('prestataire' . ($i + 1)));
            $etablissement->setHorairesOuverture("{\"lundi\":{\"checked\":true,\"timeRange\":{\"startTime\":\"09:00\",\"endTime\":\"19:00\"}},\"mardi\":{\"checked\":true,\"timeRange\":{\"startTime\":\"09:00\",\"endTime\":\"19:00\"}},\"mercredi\":{\"checked\":true,\"timeRange\":{\"startTime\":\"09:00\",\"endTime\":\"19:00\"}},\"jeudi\":{\"checked\":false,\"timeRange\":{\"startTime\":\"\",\"endTime\":\"\"}},\"vendredi\":{\"checked\":false,\"timeRange\":{\"startTime\":\"\",\"endTime\":\"\"}},\"samedi\":{\"checked\":false,\"timeRange\":{\"startTime\":\"\",\"endTime\":\"\"}},\"dimanche\":{\"checked\":false,\"timeRange\":{\"startTime\":\"\",\"endTime\":\"\"}}}");
            $etablissement->setLatitude($lats[$i]);
            $etablissement->setLongitude($lngs[$i]);
            $etablissement->setVille($villes[$i]);
            $etablissement->setCodePostal($codesPostaux[$i]);

            for ($j = 0; $j < 10; $j++) {
                $copyFileName = uniqid() . '_' . 'etablissement' . $j . '.jpg';
                $originalFilePath = 'public/fixtures/etablissement' . $j . '.jpg';
                $copyFilePath = $this->fileUploader->getTargetDirectory() . '/' . $copyFileName;
                $this->filesystem->copy($originalFilePath, $copyFilePath, true);

                $file = new UploadedFile(
                    $copyFilePath,
                    $copyFileName,
                    'image/jpeg', // Adjust the mime type as necessary
                    null,
                    true
                );

                // Upload the copied file
                $imageEtablissementName = $this->fileUploader->upload($file);

                // Create and persist ImageEtablissement entity
                $imageEtablissement = new ImageEtablissement();
                $imageEtablissement->setImageName($imageEtablissementName);
                $imageEtablissement->setEtablissement($etablissement);
                $manager->persist($imageEtablissement);

                $employe = new Employe();
                $employe->setNom($nomsEmploye[$j]);
                $employe->setPrenom($prenomsEmploye[$j]);
                $employe->setEtablissement($etablissement);
                $employe->setDescription("Je m'appelle " . $employe->getPrenom() . " " . $employe->getNom() . " et je suis employé dans l'établissement " . $etablissement->getNom() . ".");
                $this->addReference('employe' . ($j + 1) . 'etablissement' . ($i + 1), $employe);
                $manager->persist($employe);
            }

            $this->addReference('etablissement' . ($i + 1), $etablissement);
            $manager->persist($etablissement);
        }

        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            UserFixtures::class,
        ];
    }
}