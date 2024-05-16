<?php
declare(strict_types=1);

namespace Chlp\OhMyPage\Application;

use Chlp\OhMyPage\Repository\PageRepository;
use Chlp\OhMyPage\Router\Router;
use Exception;

class App
{
    private static App $instance;
    private PageRepository $pageRepository;

    public function __construct(
        private array $dbConfig,
    )
    {
        $this->pageRepository = new PageRepository($this->dbConfig);
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