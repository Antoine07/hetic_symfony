<?php
namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Quote;
use Faker;

class QuoteFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $faker =  Faker\Factory::create('fr_FR');
        $faker->addProvider(new \DavidBadura\FakerMarkdownGenerator\FakerProvider($faker));

        for ($i = 0; $i < 10; $i++) {
            $post = new Quote;
            $post->setTitle($faker->catchPhrase)
                ->setContent($faker->markdown);

            $manager->persist($post);
        }

        $manager->flush();
    }

    // public function getDependencies()
    // {
    //     return array(
    //         AppFixtures::class,
    //     );
    // }
}
