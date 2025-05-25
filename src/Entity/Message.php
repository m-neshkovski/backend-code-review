<?php

namespace App\Entity;

use App\Enum\MessageStatus;
use App\Repository\MessageRepository;
use DateTime;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\SerializedName;

#[ORM\Entity(repositoryClass: MessageRepository::class)]
class Message
{
    /**
     * The entity is more secure by using UUID as the primary key.
     * The $uuid property is removed to avoid duplication.
     * The $id property is masked to display as $uuid in a serialized/normalized response.
     */
    #[ORM\Id]
    #[ORM\Column(type: Types::GUID)]
    #[Groups(['message_list'])]
    #[SerializedName('uuid')]
    private string $id;

    #[ORM\Column(type: 'string', length: 255)]
    #[Groups(['message_list'])]
    private ?string $text = null;

    /**
     * Status has a discrete value 'sent' or 'read' so it is an ENUM TYPE
     * It is not a good practice to allow it to be nullable,
     * so a default will be set in constructor to 'sent'.
     * It is also mutable, so a setter is available.
     */
    #[ORM\Column(type: 'string', enumType: MessageStatus::class)]
    #[Groups(['message_list'])]
    private MessageStatus $status;

    #[ORM\Column(type: 'datetime')]
    private DateTime $createdAt;

    public function __construct()
    {
        // ID and createdAt are immutable, this simplifies Message instantiation,
        // avoids unnecessary null checks and makes it easier to test.
        $this->id = Uuid::v4()->toRfc4122();
        $this->createdAt = new DateTime();
        // We want to set a default status to 'sent' since it is the default in SendMessageHandler
        $this->status = MessageStatus::SENT;
    }


    public function getId(): string
    {
        return $this->id;
    }

    public function getText(): ?string
    {
        return $this->text;
    }

    public function setText(string $text): self
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
