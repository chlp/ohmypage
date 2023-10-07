<?php
declare(strict_types=1);

namespace Chlp\Telepage\Router;

use Chlp\Telepage\Router\Handlers\PageReader;
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
        $this->uri = trim($this->uri, '/');
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
        if ($this->path[0] === 'edit') {
            $handler = new Handler();
            $handler->setHtml('edit');
            return $handler;
        }
        return new PageReader($this->path[0]);
    }
}