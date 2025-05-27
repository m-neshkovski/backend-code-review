<?php

namespace App\Enum;

enum MessageStatus: string
{
    case SENT = 'sent';
    case READ = 'read';

    /**
     * Returns all the values of the enum.
     *
     * @return array<string>
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    /**
     * Returns random value of the enum.
     * This insures that a correct enum value is used in fixtures, tests and so on.
     */
    public static function random(): MessageStatus
    {
        return self::cases()[array_rand(self::cases())];
    }

    /**
     * This function is a validation for the 'status' query parameter.
     * Now used in MessageController to ensure that only the correct
     * parameter is passed to filterByStatus function.
     */
    public static function isValidForFilterByStatus(?string $status): bool
    {
        return empty($status) || in_array($status, self::values());
    }
}
