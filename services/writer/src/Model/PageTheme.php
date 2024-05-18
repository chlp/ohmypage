<?php
declare(strict_types=1);

namespace Chlp\OhMyPage\Model;

trait PageTheme
{
    public const THEME_AIR = 'air';
    public const THEME_MODEST = 'modest';
    public const THEME_RETRO = 'retro';
    public const THEME_SPLENDOR = 'splendor';

    public static function getThemes(): array
    {
        return [self::THEME_AIR, self::THEME_MODEST, self::THEME_RETRO, self::THEME_SPLENDOR];
    }
}