<?php
declare(strict_types=1);

namespace Chlp\Telepage\Models;

use DateTime;
use Parsedown;

class Page
{
    private const STATUS_PUBLIC = 1;
    private const STATUS_PRIVATE = 2;
    private const STATUS_DELETED = 3;

    /**
     * @param string $id
     * @param DateTime $created
     * @param string $title
     * @param string $content
     * @param int $status
     * @param string $theme
     * @param string $lang
     * @param string[] $images
     */
    public function __construct(
        public string   $id,
        public DateTime $created,
        public string   $title,
        public string   $content,
        public int      $status,
        public string   $theme,
        public string   $lang,
        public array    $images,
    )
    {
    }

    public function makeHtml(): string
    {
        $mdParser = new Parsedown();
        return $this->getHtmlHeader() . $mdParser->text($this->content) . $this->getHtmlFooter();
    }

    private function getHtmlHeader(): string
    {
        return '<!doctype html>
<html lang="' . $this->lang . '">
<head>
    <meta charset="UTF-8">
    <title>' . $this->title . '</title>
    <link rel="stylesheet" href="/css/' . $this->theme . '.css">
</head>
<body>

<h1>' . $this->title . '</h1>
';
    }

    private function getHtmlFooter(): string
    {
        return '
</body>
</html>';
    }
}