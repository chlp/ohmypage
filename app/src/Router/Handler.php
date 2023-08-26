<?php
declare(strict_types=1);

namespace Chlp\Telepage\Router;

use Exception;

class Handler
{
    private ?string $html = null;

    /**
     * @throws Exception
     */
    public function run(): void
    {
        $this->setBasicHeaders();
        if ($this->html !== null) {
            header('Content-Type:text/html; charset=UTF-8');
            echo $this->html;
        } else {
            throw new Exception('wrong handler output');
        }
    }

    public function setHtml(string $html): void
    {
        $this->html = $html;
    }

    private function setBasicHeaders(): void
    {
        header('Cache-Control: max-age=86400');
    }
}