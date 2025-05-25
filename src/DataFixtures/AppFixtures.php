<?php

namespace App\DataFixtures;

use App\Entity\Message;
use App\Enum\MessageStatus;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

use function Psl\Iter\random;

class AppFixtures extends Fixture
{
    /**
     * This method is simplified to match the new Message entity class.
     */
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();

        foreach (range(1, 10) as $i) {
            $message = new Message();
            $message->setText($faker->sentence);
            $message->setStatus(random([MessageStatus::SENT, MessageStatus::READ]));

            $manager->persist($message);
        }

        $manager->flush();
    }
}
