<?php
declare(strict_types=1);

namespace Chlp\Telepage\Router;

use Chlp\Telepage\Router\Handlers\Reader;
use Exception;

class Router
{
    /**
     * @param string $uri
     */
    public function __construct(
        private string $uri
    )
    {
    }

    /**
     * @return Handler
     * @throws Exception
     */
    public function getHandler(): Handler
    {
        $this->uri = trim(trim($this->uri), '/');
        if ($this->uri === 'edit') {
            $handler = new Handler();
            $handler->setHtml('edit');
            return $handler;
        }
        return new Reader($this->uri);
    }
}