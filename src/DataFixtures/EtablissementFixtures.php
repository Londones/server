<?php

namespace App\DataFixtures;

use App\Entity\Etablissement;
use App\Entity\ImageEtablissement;
use App\Entity\Employe;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Faker\Factory;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class EtablissementFixtures extends Fixture implements DependentFixtureInterface
{
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
            48.8428732,
            48.8643964,
            48.8471871,
            48.8459719,
            48.8616437,
            48.8707028,
            48.8719778,
            48.8760842,
            48.8997316,
            48.8965983,
            48.8872136
        ];

        $lngs = [
            2.3996645,
            2.3786389,
            2.3870859,
            2.2775704,
            2.3648959,
            2.3335596,
            2.3707953,
            2.3227861,
            2.2875711,
            2.3287002,
            2.3541341
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
        for ($i = 1; $i <= 10; $i++) {
            $etablissement = new Etablissement;
            $etablissement->setNom($noms[$i - 1]);
            $etablissement->setAdresse($adresses[$i - 1]);
            $etablissement->setKbis("Kbis-" . $i);
            $etablissement->setValidation(true);
            $etablissement->setPrestataire($this->getReference('prestataire' . $i));
            $etablissement->setHorrairesOuverture("-,10:00-19:00,10:00-19:00,10:00-20:00,10:00-19:00,10:00-19:00,-");
            $etablissement->setJoursOuverture("Lundi,Mardi,Mercredi,Jeudi,Vendredi,Samedi,Dimanche");
            $etablissement->setLatitude($lats[$i - 1]);
            $etablissement->setLongitude($lngs[$i - 1]);
            $etablissement->setVille($villes[$i - 1]);
            $etablissement->setCodePostal($codesPostaux[$i - 1]);


            for ($j = 1; $j <= 10; $j++) {
                $imageEtablissement = new ImageEtablissement();
                $imageEtablissement->setImageName('image' . $j . '.jpg');
                $file = new UploadedFile(
                    'public/fixtures/etablissement' . $j - 1 . '.jpg',
                    'image.png',
                    'image/png',
                    null,
                    true
                );
                $imageEtablissement->setImageFile($file);
                $imageEtablissement->setEtablissement($etablissement);
                $manager->persist($imageEtablissement);

                $employe = new Employe();
                $employe->setNom($nomsEmploye[$j - 1]);
                $employe->setPrenom($prenomsEmploye[$j - 1]);
                $employe->setEtablissement($etablissement);
                $employe->setDescription("Je m'appelle " . $employe->getPrenom() . " " . $employe->getNom() . " et je suis employé dans l'établissement " . $etablissement->getNom() . ".");
                $this->addReference('employe' . $j . 'etablissement' . $i, $employe);
                $manager->persist($employe);
            }

            $this->addReference('etablissement' . $i, $etablissement);
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
