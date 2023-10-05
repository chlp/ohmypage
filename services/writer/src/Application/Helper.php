<?php
declare(strict_types=1);

namespace Chlp\Telepage\Application;

use Exception;

class Helper
{
    public static function isUuid(string $uuid): bool
    {
        return preg_match('/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/', $uuid) === 1;
    }

    const A_CHAR_INT = 97;

    /**
     * @throws Exception
     */
    public static function intToOhMyChar(int $i): string
    {
        if ($i < 0) {
            throw new Exception("wrong num 1 ($i)");
        } else if ($i < 10) {
            return ((string)$i);
        } else if ($i > 35) {
            throw new Exception("wrong num 2 ($i)");
        } else {
            return (chr($i - 10 + self::A_CHAR_INT));
        }
    }
}