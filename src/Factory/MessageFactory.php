<?php

namespace App\Factory;

use App\Entity\Message;
use App\Enum\MessageStatus;
use Faker\Factory;

class MessageFactory
{
    /**
     * Instantiates one Message Entity. The status is set to the default value 'sent'.
     * Better to have a default set than IF conditional checks for null etc.
     */
    public function create(string $text, MessageStatus $status = MessageStatus::SENT): Message
    {
        $message = new Message();
        $message->setText($text);
        $message->setStatus($status);

        return $message;
    }

    /**
     * Instantiate one or more fake messages for testing etc.
     *
     * @return array<Message>
     */
    public function createFakeMessages(int $messageCount): array
    {
        if ($messageCount > 0) {
            $faker = Factory::create();
            $messages = [];

            for ($i = 0; $i < $messageCount; ++$i) {
                $messages[] = $this->create($faker->sentence(), MessageStatus::random());
            }

            return $messages;
        }

        return [];
    }
}
