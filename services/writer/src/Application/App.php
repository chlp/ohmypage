<?php
declare(strict_types=1);

namespace Chlp\OhMyPage\Application;

use Chlp\OhMyPage\Repository\ImageRepository;
use Chlp\OhMyPage\Repository\PageRepository;
use Chlp\OhMyPage\Router\Router;
use Exception;
use MongoDB\Database;
use MongoDB\Client;

class App
{
    private static self $instance;
    private Database $db;
    private PageRepository $pageRepository;
    private ImageRepository $imageRepository;

    private function __construct()
    {
        $mongodbClient = new Client(DB_CONFIG['URL']);
        $this->db = $mongodbClient->selectDatabase(DB_CONFIG['DB']);

        $this->imageRepository = ImageRepository::get($this->db);
        $this->pageRepository = PageRepository::get($this->db);

        self::$instance = $this;
    }

    public static function get(): self
    {
        if (!isset(self::$instance)) {
            return new self();
        }
        return self::$instance;
    }

    public static function getPageRepository(): PageRepository
    {
        return self::get()->pageRepository;
    }

    public static function getImageRepository(): ImageRepository
    {
        return self::get()->imageRepository;
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