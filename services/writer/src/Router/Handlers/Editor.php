<?php
declare(strict_types=1);

namespace Chlp\OhMyPage\Router\Handlers;

use Chlp\OhMyPage\Application\App;
use Chlp\OhMyPage\Application\Helper;
use Chlp\OhMyPage\Model\Page;
use Chlp\OhMyPage\Router\Handler;
use DateTime;
use Exception;

class Editor extends Handler
{
    /**
     * @throws Exception
     */
    public function __construct(private $pageId)
    {
        if ($this->pageId !== '' && !Helper::isUuid($this->pageId)) {
            throw new Exception('wrong id');
        }
    }

    public function run(): void
    {
        if ($this->pageId !== '') {
            $pageRepository = App::getPageRepository();
            $page = $pageRepository->getById($this->pageId);
            if ($page === null) {
                throw new Exception('page not found');
            }
        } else {
            $page = new Page(
                Helper::genUuid(),
                new DateTime(),
                '',
                '',
                Page::STATUS_PRIVATE,
                Page::THEME_AIR,
                [],
            );
        }
        $this->setHtml($page->makeHtml());
        parent::run();
    }
}