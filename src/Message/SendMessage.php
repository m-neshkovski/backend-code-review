<?php
declare(strict_types=1);

namespace App\Message;

class SendMessage
{
    /**
     * @param string $text
     */
    public function __construct(
        public string $text,
    )
    {
    }
}