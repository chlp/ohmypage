<?php
declare(strict_types=1);

namespace Chlp\OhMyPage\Repository;

use Chlp\OhMyPage\Model\Page;
use DateTime;
use MongoDB\Database;
use MongoDB\Client;


class PageRepository
{
    private const PAGE_COLLECTION = 'pages';

    private Database $db;

    public function __construct(
        private array $dbConfig,
    )
    {
        $mongodbClient = new Client($this->dbConfig['URL']);
        $this->db = $mongodbClient->selectDatabase($this->dbConfig['DB']);
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