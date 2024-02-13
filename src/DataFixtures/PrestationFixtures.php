<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Prestation;
use App\Entity\Reservation;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Faker\Factory;

class PrestationFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $serviceTitles = [
            'Service de compagnie',
            'Service de soutien émotionnel',
            'Ami ou amie à louer',
            'Service de conversation',
            'Service de partage de loisirs',
            'Cours de discussion',
            'Service de copain ou copine',
            'Service d\'écoute empathique',
            'Service de conseil amical',
            'Service de rencontre',
            'Anniversaire',
            'Escorte soirée',
        ];

        $categoryNames = [
            'Compagnie',
            'Soutien émotionnel',
            'Communication',
            'Partage d\'activités',
            'Occasions spéciales',
        ];

        $status = [
            'created',
            'canceled'
        ];

        $creneaux = [
            '10:00',
            '10:30',
            '11:00',
            '11:30',
            '14:00',
            '14:30',
            '15:00',
            '15:30',
            '16:00',
            '16:30',
        ];


        $jours = [
            '10/02/2024',
            '15/02/2024',
            '20/02/2024',
            '02/03/2024',
            '09/03/2024',
            '12/03/2024',
            '19/03/2024',
            '20/03/2024',
            '29/03/2024',
            '05/04/2024',
            '09/05/2024',
            '10/05/2024',
            '19/06/2024',
            '19/07/2024',
            '25/07/2024',
        ];

        for ($i = 1; $i <= 50; $i++) {
            $Prestation = new Prestation();
            $randomTitle = $serviceTitles[array_rand($serviceTitles)]; // Select a random title
            $Prestation->setTitre($randomTitle);
            $Prestation->setDescription("Le Lorem Ipsum esSt simplement du faux texte employé dans la composition et la mise en page avant impression. Le Lorem Ipsum est le faux texte standard de l'imprimerie depuis les années 1500");
            $Prestation->setPrix(rand(10, 100));
            $Prestation->setDuree(rand(15, 180));
            $Prestation->setNoteGenerale(rand(1, 500) / 100);

            $this->addReference('prestation' . $i, $Prestation);

            $randomCategory = $categoryNames[array_rand($categoryNames)];
            $Prestation->setCategory($this->getReference('category' . $randomCategory));

            $randomEtablissement = rand(1, 10);
            $Prestation->setEtablissement($this->getReference('etablissement' . $randomEtablissement));
            $Prestation->addEmploye($this->getReference('employe' . $randomEtablissement . 'etablissement' . $randomEtablissement));

            $Reservation = new Reservation();
            $randomClient = rand(1, 20);
            $Reservation->setClient($this->getReference('user' . $randomClient));
            $Reservation->setPrestation($Prestation);
            $Reservation->setEmploye($this->getReference('employe' . $randomEtablissement . 'etablissement' . $randomEtablissement));
            $randomStatus = $status[array_rand($status)];
            $Reservation->setStatus($randomStatus);
            $randomCrenau = $creneaux[array_rand($creneaux)];
            $Reservation->setCreneau($randomCrenau);
            $Reservation->setDuree(30);
            $randomJour = $jours[array_rand($jours)];
            $Reservation->setJour($randomJour);

            $manager->persist($Prestation);
            $manager->persist($Reservation);

        }

        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            EtablissementFixtures::class,
            CategoryFixtures::class,
            UserFixtures::class,
        ];
    }
}