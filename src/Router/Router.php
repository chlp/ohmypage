<?php
declare(strict_types=1);

namespace Chlp\Telepage\Router;

use Chlp\Telepage\Router\Handlers\PageReader;
use Exception;

class Router
{
    private const HOME_PATH = '/editor/';

    /**
     * @param string $uri
     */
    public function __construct(
        private string $uri
    )
    {
        $this->uri = substr($this->uri, strlen(self::HOME_PATH));
    }

    /**
     * @return Handler
     * @throws Exception
     */
    public function getHandler(): Handler
    {
        if ($this->uri === 'edit') {
            $handler = new Handler();
            $handler->setHtml('edit');
            return $handler;
        }
        return new PageReader($this->uri);
    }
}