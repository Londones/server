<?php

namespace App\DataFixtures;

use App\Entity\Critere;
use App\Entity\Category;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class CriteresFixtures extends Fixture
{
 public function load(ObjectManager $manager): void
 {
  $categories = $manager->getRepository(Category::class)->findAll();

  $criteres = [
   'Ponctualité',
   'Respect',
   'Communication',
   'Bienveillance',
  ];

  foreach ($criteres as $critereName) {
   $existingCritere = $manager->getRepository(Critere::class)->findOneBy(['titre' => $critereName]);

   if (!$existingCritere) {
    $critere = new Critere();
    $critere->setTitre($critereName);
    $manager->persist($critere);
   } else {
    $critere = $existingCritere;
   }

   foreach ($categories as $category) {
    $category->addCritere($critere);
   }
  }

  $manager->flush();
 }
}
