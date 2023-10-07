<?php
declare(strict_types=1);

namespace Chlp\Telepage\Application;

use Chlp\Telepage\Repositories\PageRepository;
use Chlp\Telepage\Router\Router;
use Exception;
use Medoo\Medoo;

class App
{
    private static App $instance;
    private PageRepository $pageRepository;

    /**
     * @param Medoo $database
     */
    public function __construct(
        private Medoo $database,
    )
    {
        $this->pageRepository = new PageRepository($this->database);
        self::$instance = $this;
    }

    /**
     * @throws Exception
     */
    public static function getInstance(): App
    {
        if (self::$instance === null) {
            throw new Exception('no instance');
        }
        return self::$instance;
    }

    public function getPageRepository(): PageRepository
    {
        return $this->pageRepository;
    }

    public function run(): void
    {
        $router = new Router($_SERVER['REQUEST_URI']);
        try {
            $handler = $router->getHandler();
            $handler->run();
        } catch (Exception $ex) {
            echo '<pre>' . $ex->getMessage() . '</pre>';
        }
    }
}