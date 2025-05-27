<?php

namespace App\DataFixtures;

use App\Factory\MessageFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    /**
     * This method is simplified to match the new Message entity class.
     * It is decoupled from the Message class. Instead, we use MessageFactory,
     * which handles Message class object instantiation.
     */
    public function load(ObjectManager $manager): void
    {
        $messageFactory = new MessageFactory();

        foreach ($messageFactory->createFakeMessages(10) as $message) {
            $manager->persist($message);
        }

        $manager->flush();
    }
}
