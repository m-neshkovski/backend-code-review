<?php

declare(strict_types=1);

namespace App\Message;

use App\Factory\MessageFactory;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class SendMessageHandler
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private MessageFactory $messageFactory,
        private LoggerInterface $logger,
    ) {
    }

    /**
     * When a message is sent, only the text is important since we set status to default value 'sent'.
     */
    public function __invoke(SendMessage $sendMessage): void
    {
        try {
            $message = $this->messageFactory->create($sendMessage->text);

            $this->entityManager->persist($message);
            $this->entityManager->flush();
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
        }
    }
}
