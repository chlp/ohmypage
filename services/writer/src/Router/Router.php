<?php
declare(strict_types=1);

namespace Chlp\OhMyPage\Router;

use Chlp\OhMyPage\Application\Helper;
use Chlp\OhMyPage\Router\Handlers\Editor;
use Chlp\OhMyPage\Router\Handlers\Save;
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
        return match ($this->path[0]) {
            'save' => new Save($this->path[1] ?? ''),
            default => new Editor($this->path[0]),
        };
    }
}