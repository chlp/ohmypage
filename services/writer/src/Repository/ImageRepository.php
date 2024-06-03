<?php
declare(strict_types=1);

namespace Chlp\OhMyPage\Repository;

use Chlp\OhMyPage\Model\Image;
use DateTime;
use MongoDB\Database;

class ImageRepository
{
    private const IMAGE_COLLECTION = 'images';
    private static self $instance;

    private function __construct(
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
            DateTime::createFromFormat('Y-m-d H:i:s', $row['created'] ?? date('Y-m-d H:i:s')),
            $row['width'],
            $row['height'],
            $row['format'],
            $row['thumbnail'],
            $row['hash'] ?? 'empty',
        );
    }

    public function save(Image $image): void
    {
        // todo: use result
        $this->db->selectCollection(self::IMAGE_COLLECTION)->updateOne(
            ['id' => $image->id],
            [
                '$set' => [
                    'created' => $image->created->format('Y-m-d H:i:s'),
                    'width' => $image->width,
                    'height' => $image->height,
                    'format' => $image->format,
                    'thumbnail' => $image->thumbnail,
                    'hash' => $image->hash,
                ]
            ],
            ['upsert' => true]
        );
    }
}