<?php
declare(strict_types=1);

namespace Chlp\Telepage\Router\Handlers;

use Chlp\Telepage\Application\App;
use Chlp\Telepage\Application\Helper;
use Chlp\Telepage\Router\Handler;
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