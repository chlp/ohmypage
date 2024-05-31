<?php
declare(strict_types=1);

namespace Chlp\OhMyPage\Router\Handlers;

use Chlp\OhMyPage\Application\App;
use Chlp\OhMyPage\Application\Helper;
use Chlp\OhMyPage\Model\Page;
use Chlp\OhMyPage\Router\Handler;
use DateTime;
use Exception;

class Save extends Handler
{
    /**
     * @throws Exception
     */
    public function __construct(private $pageId)
    {
        if ($this->getMethod() !== self::METHOD_POST) {
            throw new Exception('wrong method');
        }
        if (!Helper::isUuid($this->pageId)) {
            throw new Exception('wrong id');
        }
    }

    public function run(): void
    {
        $pageRepository = App::getPageRepository();
        $page = $pageRepository->getById($this->pageId);
        if ($page !== null) {
            $page->title = $_POST['title'] ?? $page->title;
            $page->content = $_POST['content'] ?? $page->content;
            $page->theme = $_POST['theme'] ?? Page::THEME_AIR;
        } else {
            $page = new Page(
                $this->pageId,
                new DateTime(),
                $_POST['title'] ?? 'no-title',
                $_POST['content'] ?? 'no-content',
                Page::STATUS_PRIVATE,
                $_POST['theme'] ?? Page::THEME_AIR,
                [],
            );
        }
        $pageRepository->save($page);
        header('Location: /' . $page->id);
    }
}