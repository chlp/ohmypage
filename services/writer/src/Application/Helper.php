<?php
declare(strict_types=1);

namespace Chlp\OhMyPage\Application;

use DateTime;
use Exception;

class Helper
{
    public const ID_LENGTH = 32;

    static public function getDbConfig(): array
    {
        return DB_CONFIG;
    }

    static public function getServicesConfig(): array
    {
        return SERVICES;
    }

    static public function genUuid(): string
    {
        return sprintf('%04x%04x%04x%04x%04x%04x%04x%04x',
            mt_rand(0, 0xffff), mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0x0fff) | 0x4000,
            mt_rand(0, 0x3fff) | 0x8000,
            mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
        );
    }

    static public function isUuid(string $id): bool
    {
        return preg_match('/^[a-z0-9]{' . self::ID_LENGTH . '}$/', $id) === 1;
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

    private static function datetimeToOhMyArr(DateTime $dt): array
    {
        try {
            $year = Helper::intToOhMyChar((int)$dt->format('y')); // year
        } catch (Exception $e) {
            Helper::log('Page::getVarDirBasePath() year: ' . $e->getMessage());
            $year = '_';
        }
        try {
            $month = Helper::intToOhMyChar((int)$dt->format('n')); // month
        } catch (Exception $e) {
            Helper::log('Page::getVarDirBasePath() month: ' . $e->getMessage());
            $month = '_';
        }
        try {
            $day = Helper::intToOhMyChar((int)$dt->format('j')); // day
        } catch (Exception $e) {
            Helper::log('Page::getVarDirBasePath() day: ' . $e->getMessage());
            $day = '_';
        }
        return [$year, $month, $day];
    }

    public static function datetimeToOhMyPath(DateTime $dt): string
    {
        return implode('/', self::datetimeToOhMyArr($dt));
    }

    public static function datetimeToOhMyStr(DateTime $dt): string
    {
        return implode('', self::datetimeToOhMyArr($dt));
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

    const MAX_INPUT_MESSAGE_FOR_LOG = 10240;

    public static function log(string $message): void
    {
        $inputType = 'raw';
        $input = substr(file_get_contents('php://input'), 0, self::MAX_INPUT_MESSAGE_FOR_LOG);
        $jsonInput = json_decode($input, true);
        if (is_array($jsonInput)) {
            $inputType = 'json';
            $input = json_encode($jsonInput, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        }
        file_put_contents(self::getVarDirPath() . '/logs/' . date('Y-m-d') . '.txt',
            date('Y-m-d H:i:s') . " {$_SERVER['REQUEST_METHOD']} {$_SERVER['REQUEST_URI']}\r\n" .
            ($input ? "{$inputType}: {$input}\r\n" : '') .
            $message . "\r\n\r\n", FILE_APPEND);
    }
}