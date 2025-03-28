<?php
namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use App\Entity\User;
use App\Entity\Message;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class MessageFixtures extends Fixture implements DependentFixtureInterface
{
    private $faker;
    public function __construct()
    {
        $this->faker = Factory::create("fr_FR");
    }
    public function load(ObjectManager $manager): void
    {
        for ($i = 0; $i < 10; $i++) {
            $message = new Message();
            $message->setTitre($this->faker->sentence(3));
            $message->setDatePoste($this->faker->dateTimeThisYear());
            $message->setContenu($this->faker->paragraph());
            $message->setUser($this->getReference('user'.mt_rand(0,9), User::class));           
             $manager->persist($message);
            $this->addReference('message' . $i, $message);
        }
        for ($i = 0; $i < 20; $i++) {
            $message = new Message();
            $message->setTitre($this->faker->sentence(3));
            $message->setDatePoste($this->faker->dateTimeThisYear());
            $message->setContenu($this->faker->paragraph());
            $message->setUser($this->getReference('user' . mt_rand(0, 9), User::class));
            $message->setParent($this->getReference('message' . mt_rand(0, 9),Message::class));
            $manager->persist($message);
        }
        $manager->flush();
    }
    public function getDependencies(): array
    {
        return [
            UserFixtures::class,
        ];
    }
}