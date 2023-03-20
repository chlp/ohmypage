<?php
declare(strict_types=1);

namespace Chlp\Telepage\Models;

use DateTime;

class Page
{
    private const STATUS_PUBLIC = 1;
    private const STATUS_PRIVATE = 2;
    private const STATUS_DELETED = 3;

    /**
     * @param int $id
     * @param DateTime $created
     * @param string $title
     * @param string $content
     * @param int $status
     * @param string[] $images
     */
    public function __construct(
        public int      $id,
        public DateTime $created,
        public string   $title,
        public string   $content,
        public int      $status,
        public array    $images,
    )
    {
    }
}