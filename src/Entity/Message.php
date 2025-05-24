<?php

namespace App\Entity;

use App\Enum\MessageStatus;
use App\Repository\MessageRepository;
use DateTime;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: MessageRepository::class)]
class Message
{
    /**
     * $id is immutable and AUTOINCREMENT in a database and therefore set to null
     */
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    /**
     * $uuid is immutable and therefore set to a readonly - value set on instantiations in constructor
     */
    #[ORM\Column(type: Types::GUID)]
    #[Groups(['message_list'])]
    private readonly string $uuid;

    /**
     * $text is a required and user-generated there for mutable
     */
    #[ORM\Column(type: 'string', length: 255)]
    #[Groups(['message_list'])]
    private string $text;

    /**
     * Status has a discrete value 'sent' or 'read' so it is an ENUM TYPE
     * It is not a good practice to allow it to be nullable,
     * so a default will be set in constructor to 'sent'.
     * It is also mutable
     */
    #[ORM\Column(type: 'string', enumType: MessageStatus::class)]
    #[Groups(['message_list'])]
    private MessageStatus $status;

    /**
     * createdAt is immutable and therefore set to a readonly - value set on instantiations in constructor
     */
    #[ORM\Column(type: 'datetime')]
    private readonly DateTime $createdAt;

    public function __construct()
    {
        // UUID and createdAt are immutable, this simplifies Message instantiation,
        // avoids unnecessary null checks and makes it easier to test.
        $this->uuid = Uuid::v4()->toRfc4122();
        $this->createdAt = new DateTime();
        // We want to set a default status to 'sent' since it is the default in SendMessageHandler
        $this->setStatus(MessageStatus::SENT);
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUuid(): string
    {
        return $this->uuid;
    }

    public function getText(): string
    {
        return $this->text;
    }

    public function setText(string $text): static
    {
        $this->text = $text;

        return $this;
    }

    public function getStatus(): MessageStatus
    {
        return $this->status;
    }

    public function setStatus(MessageStatus $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getCreatedAt(): DateTime
    {
        return $this->createdAt;
    }
}
