<?php

declare(strict_types=1);

namespace App\Message;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * This class serves as a container, DTO in a way to pass the necessary parameters
 * into the handler class. Sync channel is used, but this is an async job.
 * We also use this class to validate the text query parameter.
 */
readonly class SendMessage
{
    #[Assert\NotBlank, Assert\Length(max: 255)]
    public readonly string $text;

    public function __construct(string $text)
    {
        $this->text = $text;
    }
}
