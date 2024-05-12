<?php
declare(strict_types=1);

namespace Chlp\OhMyPage\Router\Handlers;

use Chlp\OhMyPage\Application\App;
use Chlp\OhMyPage\Application\Helper;
use Chlp\OhMyPage\Router\Handler;
use Exception;

class PageReader extends Handler
{
    /**
     * @throws Exception
     */
    public function __construct(private $pageId)
    {
        if (!Helper::isUuid($this->pageId)) {
            throw new Exception('wrong id');
        }
    }

    public function run(): void
    {
        $pageRepository = App::getInstance()->getPageRepository();
        $page = $pageRepository->getById($this->pageId);
        if ($page === null) {
            throw new Exception('page not found');
        }
        $this->setHtml($page->makeHtml());
        parent::run();
    }
}