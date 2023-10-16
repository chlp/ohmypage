<?php
declare(strict_types=1);

namespace Chlp\Telepage\Router;

use Chlp\Telepage\Application\Helper;
use Chlp\Telepage\Router\Handlers\PageReader;
use Chlp\Telepage\Router\Handlers\TelegramWebHook;
use Exception;

class Router
{
    private array $path;

    /**
     * @param string $uri
     */
    public function __construct(
        private string $uri
    )
    {
        $this->uri = strtok(trim($this->uri, '/'), '?') ?: '';
        $this->path = explode('/', $this->uri);
        if (count($this->path) === 0) {
            $this->path = [''];
        }
    }

    /**
     * @return Handler
     * @throws Exception
     */
    public function getHandler(): Handler
    {
        switch ($this->path[0]) {
            case 'edit':
                $handler = new Handler();
                $handler->setHtml('edit');
                return $handler;
            case 'telegram_webhook':
                return new TelegramWebHook();
            default:
                return new PageReader($this->path[0]);
        }
    }
}