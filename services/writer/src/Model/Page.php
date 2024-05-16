<?php
declare(strict_types=1);

namespace Chlp\OhMyPage\Model;

use Chlp\OhMyPage\Application\Helper;
use Exception;
use DateTime;
use Parsedown;

class Page
{
    public const STATUS_PUBLIC = 1;
    public const STATUS_PRIVATE = 2;
    public const STATUS_DELETED = 3;

    public const THEME_AIR = 'air';
    public const THEME_MODEST = 'modest';
    public const THEME_RETRO = 'retro';
    public const THEME_SPLENDOR = 'splendor';

    /**
     * @param string $id
     * @param DateTime $created
     * @param string $title
     * @param string $content
     * @param int $status
     * @param string $theme
     * @param string[] $images
     */
    public function __construct(
        public string $id,
        public DateTime $created,
        public string $title,
        public string $content,
        public int $status,
        public string $theme,
        public array $images,
    )
    {
    }

    public function makeHtml(): string
    {
        $mdParser = new Parsedown();
        $readerHtml = $this->getHtmlReaderHeader() . $mdParser->text($this->content) . $this->getHtmlReaderFooter();
        $writerHtml = $this->getHtmlWriterHeader() . htmlspecialchars($this->content) . $this->getHtmlWriterFooter();
        $this->saveToFile($readerHtml);
        return $writerHtml;
    }

    private function saveToFile(string $html): void
    {
        // todo: check if there is no any pages with the same latin name today

        $pageHtmlPath = $this->getHtmlFilePath();
        $pageJsonPath = $this->getJsonFilePath();

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
            if (!is_dir(dirname($pageHtmlPath))) {
                mkdir(dirname($pageHtmlPath), 0777, true);
            }
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

    private function getPagePath(): string
    {
        try {
            $year = Helper::intToOhMyChar((int)$this->created->format('y')); // year
        } catch (Exception $e) {
            Helper::log('Page::getVarDirBasePath() year: ' . $e->getMessage());
            $year = '_';
        }
        try {
            $month = Helper::intToOhMyChar((int)$this->created->format('n')); // month
        } catch (Exception $e) {
            Helper::log('Page::getVarDirBasePath() month: ' . $e->getMessage());
            $month = '_';
        }
        try {
            $day = Helper::intToOhMyChar((int)$this->created->format('j')); // day
        } catch (Exception $e) {
            Helper::log('Page::getVarDirBasePath() day: ' . $e->getMessage());
            $day = '_';
        }
        $datePath = "$year/$month/$day";
        return "$datePath/" . $this->getLatinName();
    }

    private function getHtmlReaderPath(): string
    {
        return $this->getPagePath() . '.html';
    }

    private function getVarDirBasePath(): string
    {
        return Helper::getVarDirPath() . '/generated_pages/' . $this->getPagePath();
    }

    private function getHtmlFilePath(): string
    {
        return $this->getVarDirBasePath() . '.html';
    }

    private function getJsonFilePath(): string
    {
        return $this->getVarDirBasePath() . '.json';
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

    private function getHtmlWriterHeader(): string
    {
        return '<!doctype html>
<html>
<head>
    <meta charset="UTF-8">
    <title>' . $this->title . '</title>
    <link rel="icon" href="/favicon.ico">
</head>
<body>

<form method="post" action="/save/' . $this->id . '">
Title: <input type="text" name="title" value="' . $this->title . '">
<br>
<textarea name="content" style="min-width: 800px; min-height: 500px; width: 60vw; height: 70vh; margin: 1em;">
';
    }

    private function getHtmlWriterFooter(): string
    {
        return '
</textarea>
<br>
<input type="submit" value="save">
</form>
<br>
<a href="http://localhost:8131/' . $this->getHtmlReaderPath() . '">http://localhost:8131/' . $this->getHtmlReaderPath() . '</a>
<br>
</body>
</html>';
    }
}