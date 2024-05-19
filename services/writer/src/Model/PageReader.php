<?php
declare(strict_types=1);

namespace Chlp\OhMyPage\Model;

use Chlp\OhMyPage\Application\App;
use Chlp\OhMyPage\Application\Helper;
use Parsedown;

trait PageReader
{
    public const OhMyPageImgMdTag = 'OhMyPageImg';

    private function getHtmlReaderPath(): string
    {
        return $this->getPagePath() . '.html';
    }

    private function getReaderHtml(): string
    {
        $mdParser = new Parsedown();
        $html = '<!doctype html>
<html>
<head>
    <meta charset="UTF-8">
    <title>' . $this->title . '</title>
    <link rel="icon" href="/favicon.ico">
    <link rel="stylesheet" href="/template/' . $this->theme . '.css">
</head>
<body>

<h1>' . $this->title . '</h1>
';
        $html .= $mdParser->text($this->replaceOhMyImgMdWithHtml($this->content));
        $html .= '
</body>
</html>';
        return $html;
    }

    private static function replaceOhMyImgMdWithHtml(string $md): string
    {
        $pattern = '/!\[' . self::OhMyPageImgMdTag . '\]\(([a-z0-9]{' . Helper::ID_LENGTH . '})\)/';
        $callback = function ($matches) {
            $id = $matches[1];
            $image = App::getImageRepository()->getById($id);
            if ($image === null) {
                return $matches[0];
            }
            return $image->getHtmlImg();
        };
        return preg_replace_callback($pattern, $callback, $md);
    }
}