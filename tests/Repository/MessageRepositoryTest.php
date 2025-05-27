<?php

declare(strict_types=1);

namespace App\Tests\Repository;

use App\Entity\Message;
use App\Enum\MessageStatus;
use App\Factory\MessageFactory;
use App\Repository\MessageRepository;
use PHPUnit\Framework\TestCase;

class MessageRepositoryTest extends TestCase
{
    public function testFilterByStatusReturnsAllMessagesWhenStatusIsNull(): void
    {
        // Create a partial mock of MessageRepository
        $repository = $this->getMockBuilder(MessageRepository::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['findAll', 'findBy'])
            ->getMock();

        // Set up expected messages
        $messages = [
            $this->createMessage('Message 1', MessageStatus::SENT),
            $this->createMessage('Message 2', MessageStatus::READ),
        ];

        // Configure the mock to return all messages when findAll is called
        $repository->expects($this->once())
            ->method('findAll')
            ->willReturn($messages);

        // Configure the mock to never call findBy when status is null
        $repository->expects($this->never())
            ->method('findBy');

        // Call the method under test
        $result = $repository->filterByStatus(null);

        // Assert that all messages are returned
        $this->assertSame($messages, $result);
        $this->assertCount(2, $result);
    }

    public function testFilterByStatusFiltersMessagesByStatus(): void
    {
        // Create a partial mock of MessageRepository
        $repository = $this->getMockBuilder(MessageRepository::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['findAll', 'findBy'])
            ->getMock();

        // Set up expected messages
        $sentMessage = $this->createMessage('Sent message', MessageStatus::SENT);

        // Configure the mock to never call findAll when status is provided
        $repository->expects($this->never())
            ->method('findAll');

        // Configure the mock to return filtered messages when findBy is called with status criteria
        $repository->expects($this->once())
            ->method('findBy')
            ->with(['status' => 'sent'])
            ->willReturn([$sentMessage]);

        // Call the method under test
        $result = $repository->filterByStatus('sent');

        // Assert that only messages with the specified status are returned
        $this->assertCount(1, $result);
        $this->assertEquals('Sent message', $result[0]->getText());
        $this->assertEquals(MessageStatus::SENT, $result[0]->getStatus());
    }

    public function testFilterByStatusHandlesInvalidStatus(): void
    {
        // Create a partial mock of MessageRepository
        $repository = $this->getMockBuilder(MessageRepository::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['findAll', 'findBy'])
            ->getMock();

        // Configure the mock to never call findAll or findBy for invalid status
        $repository->expects($this->never())
            ->method('findAll');

        $repository->expects($this->never())
            ->method('findBy');

        // Call the method under test with an invalid status
        $result = $repository->filterByStatus('invalid_status');

        // Assert that an empty array is returned
        $this->assertSame([], $result);
    }

    public function testFilterByStatusReturnsEmptyArrayWhenNoMessagesMatch(): void
    {
        // Create a partial mock of MessageRepository
        $repository = $this->getMockBuilder(MessageRepository::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['findAll', 'findBy'])
            ->getMock();

        // Configure the mock to return empty array when findBy is called with valid status
        $repository->expects($this->once())
            ->method('findBy')
            ->with(['status' => 'read'])
            ->willReturn([]);

        // Call the method under test
        $result = $repository->filterByStatus('read');

        // Assert that an empty array is returned
        $this->assertSame([], $result);
    }

    /**
     * Helper method to create a Message entity.
     * It exists to make the status required during test.
     */
    private function createMessage(string $text, MessageStatus $status): Message
    {
        $factory = new MessageFactory();

        return $factory->create($text, $status);
    }
}
