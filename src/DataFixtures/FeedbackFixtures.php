<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use App\Entity\Feedback;
use App\Entity\Prestation;
use App\Entity\User;
use App\Entity\Critere;

class FeedbackFixtures extends Fixture implements DependentFixtureInterface
{
 public function load(ObjectManager $manager): void
 {
  $prestations = $manager->getRepository(Prestation::class)->findAll();
  $clients = $manager->getRepository(User::class)->findAll();
  $criteres = $manager->getRepository(Critere::class)->findAll();

  foreach ($prestations as $prestation) {
   foreach ($clients as $client) {
    foreach ($criteres as $critere) {
     $feedback = new Feedback();
     $feedback->setClient($client);
     $feedback->setPrestation($prestation);
     $feedback->setCritere($critere);
     $feedback->setNote(rand(1, 5));
     $manager->persist($feedback);
    }
   }
  }

  $manager->flush();
 }

 public function getDependencies()
 {
  return [
   PrestationFixtures::class,
   UserFixtures::class,
   CriteresFixtures::class,
  ];
 }
}
