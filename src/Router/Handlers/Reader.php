<?php
declare(strict_types=1);

namespace Chlp\Telepage\Router\Handlers;

use Chlp\Telepage\Application\App;
use Chlp\Telepage\Router\Handler;
use Exception;

class Reader extends Handler
{
    private int $pageId;

    /**
     * @throws Exception
     */
    public function __construct(string $pageIdString)
    {
        $this->pageId = (int)$pageIdString;
        if ($this->pageId <= 0) {
            throw new Exception('wrong id');
        }
    }

    public function run(): void
    {
        $pageRepository = App::getInstance()->getPageRepository();
        $this->setHtml('<pre' . print_r($pageRepository->getById($this->pageId), true) . '</pre>');
        parent::run();
    }
}