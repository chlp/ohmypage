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

    public static function getVarDirPath(): string
    {
        return __DIR__ . '/../../var';
    }

    const A_CHAR_CODE = 97;
    const MAX_OHMYCHAR_INT = 35;

    /**
     * @throws Exception
     */
    public static function intToOhMyChar(int $i): string
    {
        if ($i < 0) {
            throw new Exception("wrong num 1 ($i)");
        } else if ($i < 10) {
            return ((string)$i);
        } else if ($i > self::MAX_OHMYCHAR_INT) {
            throw new Exception("wrong num 2 ($i)");
        } else {
            return (chr($i - 10 + self::A_CHAR_CODE));
        }
    }

    /**
     * @throws Exception
     */
    public static function ohMyCharToInt(string $c): int
    {
        if (strlen($c) !== 1) {
            throw new Exception("wrong char len ($c)");
        }
        if ((string)(int)$c === $c) {
            return (int)$c;
        }
        $i = ord($c) + 10 - self::A_CHAR_CODE;
        if ($i < 10) {
            throw new Exception("wrong char 1 ($c)");
        } else if ($i > self::MAX_OHMYCHAR_INT) {
            throw new Exception("wrong char 2 ($c)");
        } else {
            return $i;
        }
    }

    const MAX_INPUT_MESSAGE_FOR_LOG = 1024;

    public static function log(string $message): void
    {
        $input = substr(file_get_contents('php://input'), 0, self::MAX_INPUT_MESSAGE_FOR_LOG);
        file_put_contents(self::getVarDirPath() . '/logs/' . date('Y-m-d') . '.txt',
            date('Y-m-d H:i:s') . " {$_SERVER['REQUEST_METHOD']} {$_SERVER['REQUEST_URI']}\r\n" .
            ($input ? "input: {$input}\r\n" : '') .
            $message . "\r\n\r\n", FILE_APPEND);
    }
}