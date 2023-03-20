<?php
declare(strict_types=1);

namespace Chlp\Telepage\Application;

use Chlp\Telepage\Repositories\PageRepository;
use Medoo\Medoo;

class App
{
    private PageRepository $pageRepository;

    /**
     * @param Medoo $database
     */
    public function __construct(
        private Medoo $database,
    )
    {
        $this->pageRepository = new PageRepository($this->database);
    }

    public function getPageRepository(): PageRepository
    {
        return $this->pageRepository;
    }

    public function run(): void
    {
        var_dump($this->pageRepository->getById(1));
        echo 'run';
    }
}