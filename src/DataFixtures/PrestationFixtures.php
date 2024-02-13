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

        $serviceDescriptions = [
            "Besoin de quelqu'un pour traîner? On est là pour ça! On peut regarder des films, jouer à des jeux, ou tout simplement discuter. Si tu te sens seul, fais-nous signe!",
            "Parfois, on a juste besoin de quelqu'un pour écouter, sans jugement. On est là pour ça. Tu peux te lâcher, exprimer ce que tu ressens, et on sera là pour t'écouter et te soutenir.",
            "Tu veux un pote pour t'accompagner à un truc? Que ce soit pour sortir ou juste traîner, on peut être ton ami à louer. On te promet une compagnie sympa et sans prise de tête!",
            "Tu veux discuter de tout et de rien? On est là pour ça. On peut parler de tout ce que tu veux, et on te promet une conversation sympa et enrichissante.",
            "Si tu cherches quelqu'un pour faire des activités fun ensemble, t'es au bon endroit. Que ce soit la rando, la cuisine, ou autre chose, on est partants pour partager nos loisirs avec toi!",
            "Envie d'améliorer tes talents de bavardage? On peut t'aider avec ça! On te montrera comment engager des conversations intéressantes et captivantes, promis!",
            "Si t'as besoin d'une présence amicale pour sortir ou juste chill, on peut être ton copain ou ta copine à la demande. On est là pour te tenir compagnie quand tu veux!",
            "Tu veux te confier à quelqu'un? On est tout ouïe. On saura t'écouter avec empathie et te donner le soutien dont tu as besoin, sans jugement.",
            "Si t'as besoin de conseils ou d'un avis sympa sur un truc qui te tracasse, on est là pour toi. On te donnera notre opinion avec bienveillance et sans filtre.",
            "Besoin de rencontrer de nouvelles têtes? On peut t'aider à élargir ton cercle social. Que ce soit pour l'amitié ou l'amour, on te trouvera des rencontres sympas!",
            "Si tu veux faire de ton anniversaire un événement mémorable, on est là pour t'aider à tout organiser. Des idées personnalisées pour une journée spéciale!",
            "Tu veux une escorte pour une soirée spéciale? On peut t'accompagner pour rendre cette soirée inoubliable. On te promet une compagnie agréable et attentionnée.",
        ];

        $categoryNames = [
            'Compagnie',
            'Soutien émotionnel',
            'Communication',
            'Partage d\'activités',
            'Occasions spéciales',
        ];

        for ($i = 1; $i <= 100; $i++) {
            $Prestation = new Prestation();
            $randomIndex = rand(0, 11);
            $Prestation->setTitre($serviceTitles[$randomIndex]);
            $Prestation->setDescription($serviceDescriptions[$randomIndex]);
            $Prestation->setPrix(rand(10, 100));
            $Prestation->setDuree(rand(15, 180));
            $Prestation->setNoteGenerale(rand(1, 500) / 100);

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
