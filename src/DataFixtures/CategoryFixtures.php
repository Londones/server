<?php

namespace App\DataFixtures;

use App\Entity\Category;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class CategoryFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $categoryNames = [
            'Compagnie',
            'Soutien émotionnel',
            'Communication',
            'Partage d\'activités',
            'Occasions spéciales',
        ];

        foreach ($categoryNames as $categoryName) {
            $category = new Category();
            $category->setName($categoryName);

            $this->addReference('category' . $categoryName, $category);

            $manager->persist($category);
        }

        $manager->flush();
    }
}
