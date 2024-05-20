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
<script>
    function loadImage(img) {
        if (!img instanceof HTMLImageElement) {
            return;
        }
        let fullSrc = img.dataset.src;
        console.log(fullSrc);
        let xhr = new XMLHttpRequest();
        xhr.onloadstart = (ev) => {
            console.log("onloadstart", ev);
        };
        xhr.onload = (ev) => {
            // todo: проставить blur на 0
            console.log("onload", ev);
            console.log(xhr.status); // todo: это ошибка?
            console.log(xhr);
            img.src = fullSrc;
        };
        xhr.onabort = (ev) => {
            // todo: или это ошибка? нужно проставить css свойство для неудачной загрузки и снять blur, чтобы показать thumb
            console.log("onabort", ev);
        };
        xhr.onerror = (ev) => {
            console.log("onerror", ev);
        };
        xhr.onprogress = (ev) => {
            // todo: проставить blur на процент загрузки
            console.log("onprogress", ev);
            if (ev.lengthComputable) {
                let percentLoaded = Math.round((ev.loaded / ev.total) * 100);
                console.log("Прогресс загрузки: " + percentLoaded + "%");
            }
        };
        xhr.open("GET", fullSrc);
        xhr.responseType = "blob";
        xhr.send();
    }

    for (let img of document.getElementsByClassName("ohmyimg")) {
        loadImage(img);
    }
</script>
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