<?php
namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use App\Entity\Forum;
use App\Entity\Message;


class ForumFixtures extends Fixture 
{
    private $faker;
    public function __construct()
    {
        $this->faker = Factory::create("fr_FR");
    }
    public function load(ObjectManager $manager): void
    {
        for ($i = 0; $i < 4; $i++) {
            $forum = new Forum();
            $forum->setNom($this->faker->word());
            $this->addReference('forum'.$i, $forum);
            $manager->persist($forum);
        }
       
        $manager->flush();
    }
}