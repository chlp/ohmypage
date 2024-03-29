<?php
declare(strict_types=1);

namespace Chlp\Telepage\Models;

use Chlp\Telepage\Application\Helper;
use Exception;
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

    private function getHtmlReaderFooter(): string
    {
        return '
</body>
</html>';
    }

    private function getHtmlWriterHeader(): string
    {
        // todo: hljs can not update content :(
        return '<!doctype html>
<html lang="' . $this->lang . '">
<head>
    <meta charset="UTF-8">
    <title>' . $this->title . '</title>
    <link rel="icon" href="/favicon.svg">
    
    <link rel="stylesheet" href="/highlightjs/default.min.css">
    <script src="/highlightjs/highlight.min.js"></script>
    <script src="/highlightjs/markdown.min.js"></script>
    <script>hljs.highlightAll();</script>
</head>
<body>

<h1>' . $this->title . '</h1>
<a href="http://localhost:8081/' . $this->getHtmlReaderPath() . '">http://localhost:8081/' . $this->getHtmlReaderPath() . '</a>
<br>
<pre style="min-width: 800px; min-height: 500px; width: 60vw; height: 70vh; margin: 1em;">
    <code class="language-md" style="border: 1px solid black;" contenteditable="true">
';
    }

    private function getHtmlWriterFooter(): string
    {
        return '
    </code>
</pre>
</body>
</html>';
    }
}