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
  $criteres = [
   'Ponctualité',
   'Respect',
   'Communication',
   'Bienveillance',
  ];

  foreach ($criteres as $critereName) {
   $critere = new Critere();
   $critere->setTitre($critereName);

   // Get a random category
   $categoryRepository = $manager->getRepository(Category::class);
   $categories = $categoryRepository->findAll();
   $randomCategory = $categories[array_rand($categories)];

   // Set the random category and add the critere to it
   $critere->addCategory($randomCategory);
   $randomCategory->addCritere($critere);

   $manager->persist($critere);
  }

  $manager->flush();
 }
}
