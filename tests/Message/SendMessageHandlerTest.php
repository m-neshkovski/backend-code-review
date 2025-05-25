<?php
declare(strict_types=1);

namespace Message;

use App\Entity\Message;
use App\Enum\MessageStatus;
use App\Message\SendMessage;
use App\Message\SendMessageHandler;
use App\Repository\MessageRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class SendMessageHandlerTest extends KernelTestCase
{
    public function test_when_message_is_sent_it_is_persisted_in_database(): void
    {
        self::bootKernel();
        $text = 'Hello Team "Trust" of Digistore24!';
        /**
         * @var EntityManagerInterface $entityManager
         */
        $entityManager = $this->getContainer()->get(EntityManagerInterface::class);
        $sendMessage = new SendMessage($text);
        $handler = new SendMessageHandler($entityManager);

        $handler->__invoke($sendMessage);

        $message = $entityManager->getRepository(Message::class)
            ->findOneBy(['text' => $text]);

        $this->assertNotNull($message, 'Message should be found in database');
        $this->assertEquals($text, $message->getText());
        $this->assertEquals(MessageStatus::SENT, $message->getStatus(), 'Status should be set to "sent"');

        // Clean up the database after each test
        $entityManager->createQuery('DELETE FROM App\Entity\Message m')->execute();
        $entityManager->clear();
    }
}