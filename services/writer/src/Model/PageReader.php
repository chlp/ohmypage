<?php
declare(strict_types=1);

namespace Chlp\OhMyPage\Model;

trait PageReader
{
    private function getHtmlReaderPath(): string
    {
        return $this->getPagePath() . '.html';
    }

    private function getHtmlReaderHeader(): string
    {
        return '<!doctype html>
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
    }

    private function getHtmlReaderFooter(): string
    {
        return '
</body>
</html>';
    }
}