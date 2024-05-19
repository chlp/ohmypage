<?php
declare(strict_types=1);

namespace Chlp\OhMyPage\Model;

use Chlp\OhMyPage\Application\Helper;

trait PageWriter
{
    private function getWriterHtml(): string
    {
        $html = '<!doctype html>
<html>
<head>
    <meta charset="UTF-8">
    <title>📝 ' . $this->title . '</title>
    <link rel="icon" href="/favicon.ico">
</head>
<body>

<form method="post" action="/save/' . $this->id . '">
Title: <input type="text" name="title" value="' . $this->title . '">
<br>
Theme: <select name="theme">';
        foreach (Page::getThemes() as $theme) {
            $html .= "<option value=\"$theme\"";
            if ($this->theme === $theme) {
                $html .= ' selected';
            }
            $html .= ">$theme</option>\n";
        }
        $html .= '
</select>
<br>
<textarea name="content" style="min-width: 800px; min-height: 500px; width: 60vw; height: 70vh; margin: 1em;">';
        $html .= htmlspecialchars($this->content);
        $html .= '</textarea>
<br>
<input type="submit" value="save">
</form>
<br>
<a href="' . Helper::getServicesConfig()['reader'] . $this->getHtmlReaderPath() . '">'
            . Helper::getServicesConfig()['reader'] . $this->getHtmlReaderPath() . '</a>
<br>
<br>
<a href="/">New page</a>
</body>
</html>';
        return $html;
    }
}