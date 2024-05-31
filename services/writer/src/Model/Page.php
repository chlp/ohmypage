<?php
declare(strict_types=1);

namespace Chlp\OhMyPage\Model;

use Chlp\OhMyPage\Application\Helper;
use Exception;
use DateTime;

class Page
{
    use PageReader, PageWriter, PageTheme;

    public const STATUS_PUBLIC = 1;
    public const STATUS_PRIVATE = 2;
    public const STATUS_DELETED = 3;

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
        $this->saveToFile($this->getReaderHtml()); // todo: move storing html file into saving
        return $this->getWriterHtml();
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
        return (string)preg_replace('/\s+/', '-', $this->title);
    }

    private function getVarDirBasePath(): string
    {
        return Helper::getVarDirPath() . '/generated_pages/' .
            Helper::datetimeToOhMyPath($this->created) . "/" . $this->getLatinName();
    }

    private function getHtmlFilePath(): string
    {
        return $this->getVarDirBasePath() . '.html';
    }

    private function getJsonFilePath(): string
    {
        return $this->getVarDirBasePath() . '.json';
    }
}