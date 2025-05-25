<?php
declare(strict_types=1);

namespace App\Message;

use App\Entity\Message;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
/**
 * TODO: Cover with a test
 */
class SendMessageHandler
{
    public function __construct(private EntityManagerInterface $manager)
    {
    }

    /**
     * When a message is sent, only the text is important since we set status to default value 'sent'
     * @param SendMessage $sendMessage
     */
    public function __invoke(SendMessage $sendMessage): void
    {
        $message = new Message();
        $message->setText($sendMessage->text);

        $this->manager->persist($message);
        $this->manager->flush();
    }
}