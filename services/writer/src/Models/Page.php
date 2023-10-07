<?php
declare(strict_types=1);

namespace Chlp\Telepage\Models;

use Chlp\Telepage\Application\Helper;
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
        $html = $this->getHtmlHeader() . $mdParser->text($this->content) . $this->getHtmlFooter();
        $this->saveToFile($html);
        return $html;
    }

    private function saveToFile(string $html): void
    {
        // todo: check if there is no any pages with the same latin name today
        // todo: put subdirs with year/month/day with ohMyPageChars

        $pageHtmlPath = Helper::getVarDirPath() . '/generated_pages/' . $this->getLatinName() . '.html';
        $pageJsonPath = Helper::getVarDirPath() . '/generated_pages/' . $this->getLatinName() . '.json';

        $storedMd5 = '';
        if (file_exists($pageJsonPath)) {
            $storedDataFile = file_get_contents($pageJsonPath);
            if ($storedDataFile !== false) {
                $storedData = json_decode($storedDataFile, true);
                if (is_array($storedData) && key_exists('md5', $storedData) && is_string($storedData['md5'])) {
                    $storedMd5 = (string)$storedData['md5'];
                }
            }
        }

        $newMd5 = md5($html);
        if ($newMd5 !== $storedMd5) {
            file_put_contents($pageHtmlPath, $html);
            $data = json_encode([
                'md5' => md5($html),
            ]);
            file_put_contents($pageJsonPath, $data);
        }
    }

    private function getLatinName(): string
    {
        // todo: remove all non numbers and chars
        return (string)preg_replace('/\s+/', '_', $this->title);
    }

    private function getHtmlHeader(): string
    {
        return '<!doctype html>
<html lang="' . $this->lang . '">
<head>
    <meta charset="UTF-8">
    <title>' . $this->title . '</title>
    <link rel="icon" href="/favicon.svg">
    <link rel="stylesheet" href="/template/' . $this->theme . '.css">
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