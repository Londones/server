<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Prestation;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Faker\Factory;

class PrestationFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $faker = Factory::create();

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

        for ($i = 1; $i <= 50; $i++) {
            $Prestation = new Prestation();
            $randomTitle = $serviceTitles[array_rand($serviceTitles)]; // Select a random title
            $Prestation->setTitre($randomTitle);
            $Prestation->setDescription("Le Lorem Ipsum esSt simplement du faux texte employé dans la composition et la mise en page avant impression. Le Lorem Ipsum est le faux texte standard de l'imprimerie depuis les années 1500");
            $Prestation->setPrix(rand(10, 100));
            $Prestation->setDuree(rand(15, 180));

            $this->addReference('prestation' . $i, $Prestation);

            $randomCategory = $categoryNames[array_rand($categoryNames)];
            $Prestation->setCategory($this->getReference('category' . $randomCategory));

            $randomEtablissement = rand(1, 10);
            $Prestation->setEtablissement($this->getReference('etablissement' . $randomEtablissement));
            $Prestation->addEmploye($this->getReference('employe' . $randomEtablissement . 'etablissement' . $randomEtablissement));

            $manager->persist($Prestation);
        }

        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            EtablissementFixtures::class,
            CategoryFixtures::class,
        ];
    }
}
