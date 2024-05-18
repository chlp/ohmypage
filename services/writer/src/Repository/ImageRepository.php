<?php
declare(strict_types=1);

namespace Chlp\OhMyPage\Repository;

use Chlp\OhMyPage\Model\Image;
use DateTime;
use MongoDB\Database;
use MongoDB\Client;


class ImageRepository
{
    private const IMAGE_COLLECTION = 'images';

    private Database $db;

    public function __construct(
        private readonly array $dbConfig,
    )
    {
        $mongodbClient = new Client($this->dbConfig['URL']);
        $this->db = $mongodbClient->selectDatabase($this->dbConfig['DB']);
    }

    public function getById(string $id): ?Image
    {
        $row = $this->db->selectCollection(self::IMAGE_COLLECTION)->findOne([
            'id' => $id
        ]);
        if ($row === null) {
            return null;
        }
        return new Image(
            $row['id'],
            $row['title'],
            $row['width'],
            $row['height'],
            $row['path'],
            $row['format'],
            $row['thumbnail'],
        );
    }
}