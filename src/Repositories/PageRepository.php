<?php
declare(strict_types=1);

namespace Chlp\Telepage\Repositories;

use Chlp\Telepage\Models\Page;
use DateTime;
use Medoo\Medoo;

class PageRepository
{
    private const DB_TABLE = 'pages';

    /**
     * @param Medoo $db
     */
    public function __construct(
        private Medoo $db,
    )
    {
    }

    /**
     * @param int $id
     * @return Page|null
     */
    public function getById(int $id): ?Page
    {
        $rows = $this->db->select(self::DB_TABLE, [
            'id',
            'created',
            'title',
            'content',
            'status',
        ], [
            'id' => $id
        ]);
        if ($rows === null || count($rows) !== 1) {
            return null;
        }
        $row = $rows[0];
        return new Page(
            $row['id'],
            DateTime::createFromFormat('Y-m-d H:i:s', $row['created']),
            $row['title'],
            $row['content'],
            $row['status'],
            [],
        );
    }
}