<?php

declare(strict_types=1);

namespace App\Tests\Message;

use App\Entity\Message;
use App\Enum\MessageStatus;
use App\Factory\MessageFactory;
use App\Message\SendMessage;
use App\Message\SendMessageHandler;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class SendMessageHandlerTest extends KernelTestCase
{
    private EntityManagerInterface $entityManager;
    private MessageFactory $messageFactory;
    private LoggerInterface $logger;
    private SendMessageHandler $handler;

    protected function setUp(): void
    {
        self::bootKernel();

        /**
         * @var EntityManagerInterface $entityManager
         */
        $entityManager = $this->getContainer()->get(EntityManagerInterface::class);
        $this->entityManager = $entityManager;
        /**
         * @var MessageFactory $messageFactory
         */
        $messageFactory = $this->getContainer()->get(MessageFactory::class);
        $this->messageFactory = $messageFactory;
        /**
         * @var LoggerInterface $logger
         */
        $logger = $this->getContainer()->get(LoggerInterface::class);
        $this->logger = $logger;

        $this->handler = new SendMessageHandler(
            $this->entityManager,
            $this->messageFactory,
            $this->logger
        );
    }

    protected function tearDown(): void
    {
        $this->entityManager->createQuery('DELETE FROM App\Entity\Message m')->execute();
        $this->entityManager->clear();

        parent::tearDown();
    }

    public function testWhenMessageIsSentItIsPersistedInDatabase(): void
    {
        $text = 'Hello Team "Trust" of Digistore24!';
        $sendMessage = new SendMessage($text);

        $this->handler->__invoke($sendMessage);

        $message = $this->entityManager->getRepository(Message::class)
            ->findOneBy(['text' => $text]);

        $this->assertNotNull($message, 'Message should be found in database');
        $this->assertEquals($text, $message->getText());
        $this->assertEquals(MessageStatus::SENT, $message->getStatus(), 'Status should be set to "sent"');
    }

    /**
     * @dataProvider messageTextProvider
     */
    public function testMessageWithDifferentTexts(string $text): void
    {
        $sendMessage = new SendMessage($text);

        $this->handler->__invoke($sendMessage);

        $message = $this->entityManager->getRepository(Message::class)
            ->findOneBy(['text' => $text]);

        $this->assertNotNull($message, 'Message should be found in database');
        $this->assertEquals($text, $message->getText());
    }

    /**
     * Message text provider for testMessageWithDifferentTexts, to test more cases different.
     *
     * @return array<array<string>>
     */
    public function messageTextProvider(): array
    {
        return [
            'normal text' => ['Regular message text'],
            'text with special chars' => ['Message with "quotes" and special chars: @#$%'],
            'text with emoji' => ['Message with emoji 😊'],
            'very long text' => [str_repeat('Long message ', 20)],
            'SQL injection' => ['" OR 1=1 --'],
        ];
    }

    public function testExceptionHandling(): void
    {
        // Create a mock EntityManager that throws an exception
        $mockEntityManager = $this->createMock(EntityManagerInterface::class);
        $mockEntityManager->method('persist')->willThrowException(new \Exception('Database error'));

        // Create a mock logger to verify it receives the error
        $mockLogger = $this->createMock(LoggerInterface::class);
        $mockLogger->expects($this->once())
            ->method('error')
            ->with('Database error');

        $handler = new SendMessageHandler(
            $mockEntityManager,
            $this->messageFactory,
            $mockLogger
        );

        $sendMessage = new SendMessage('Test message');

        // This should not throw an exception as it's caught inside the handler
        $handler->__invoke($sendMessage);
    }
}
