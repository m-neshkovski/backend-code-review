<?php

namespace App\Enum;

enum MessageStatus:string
{
    case SENT = 'sent';
    case READ = 'read';

    /**
     * @return array<string>
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    /**
     * @param string|null $status
     * @return bool
     */
    public static function isValidForFilterByStatus(?string $status): bool
    {
        return empty($status) || in_array($status, self::values());
    }
}
