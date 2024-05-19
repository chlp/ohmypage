<?php
declare(strict_types=1);

namespace Chlp\OhMyPage\Repository;

use Chlp\OhMyPage\Model\Page;
use DateTime;
use MongoDB\Database;

class PageRepository
{
    private const PAGE_COLLECTION = 'pages';
    private static self $instance;

    public function __construct(
        private readonly Database $db,
    )
    {
        self::$instance = $this;
    }

    public static function get(Database $db): self
    {
        if (!isset(self::$instance)) {
            return new self($db);
        }
        return self::$instance;
    }

    public function getById(string $id): ?Page
    {
        $row = $this->db->selectCollection(self::PAGE_COLLECTION)->findOne([
            'id' => $id
        ]);
        if ($row === null) {
            return null;
        }
        return new Page(
            $row['id'],
            DateTime::createFromFormat('Y-m-d H:i:s', $row['created'] ?? date('Y-m-d H:i:s')),
            $row['title'],
            $row['content'],
            $row['status'] ?? Page::STATUS_PRIVATE,
            $row['theme'] ?? Page::THEME_AIR,
            [],
        );
    }

    public function save(Page $page): void
    {
        $this->db->selectCollection(self::PAGE_COLLECTION)->updateOne(
            ['id' => $page->id],
            [
                '$set' => [
                    'created' => $page->created->format('Y-m-d H:i:s'),
                    'title' => $page->title,
                    'content' => $page->content,
                    'status' => $page->status,
                    'theme' => $page->theme,
                ]
            ],
            ['upsert' => true]
        );
    }
}