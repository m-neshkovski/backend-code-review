<?php

declare(strict_types=1);

namespace App\Tests\Factory;

use App\Entity\Message;
use App\Enum\MessageStatus;
use App\Factory\MessageFactory;
use PHPUnit\Framework\TestCase;

class MessageFactoryTest extends TestCase
{
    private MessageFactory $factory;
    private string $testText = 'Test message';

    protected function setUp(): void
    {
        $this->factory = new MessageFactory();
    }

    public function testCreateWithDefaultStatus(): void
    {
        $message = $this->factory->create($this->testText);

        $this->assertInstanceOf(Message::class, $message);
        $this->assertEquals($this->testText, $message->getText());
        $this->assertEquals(MessageStatus::SENT, $message->getStatus());
    }

    public function testCreateWithSpecificStatus(): void
    {
        $status = MessageStatus::READ;

        $message = $this->factory->create($this->testText, $status);

        $this->assertInstanceOf(Message::class, $message);
        $this->assertEquals($this->testText, $message->getText());
        $this->assertEquals($status, $message->getStatus());
    }

    public function testCreateFakeMessagesWithPositiveCount(): void
    {
        $count = 3;
        $messages = $this->factory->createFakeMessages($count);

        $this->assertCount($count, $messages);

        foreach ($messages as $message) {
            $this->assertInstanceOf(Message::class, $message);
            $this->assertNotEmpty($message->getText());
            $this->assertContains($message->getStatus(), MessageStatus::cases());
        }
    }

    /**
     * @dataProvider zeroOrNonPositiveCountProvider
     */
    public function testCreateFakeMessagesWithZeroOrNonPositiveCount(int $count): void
    {
        $messages = $this->factory->createFakeMessages($count);

        $this->assertIsArray($messages);
        $this->assertEmpty($messages);
    }

    /**
     * Data provider for testCreateFakeMessagesWithNonPositiveCount
     * Should test zero and negative values in one test.
     *
     * @return array<array<int>>
     */
    public function zeroOrNonPositiveCountProvider(): array
    {
        return [
            'zero count' => [0],
            'negative count' => [-1],
        ];
    }
}
