<?php
declare(strict_types=1);

namespace Chlp\OhMyPage\Router\Handlers;

use Chlp\OhMyPage\Application\App;
use Chlp\OhMyPage\Application\Helper;
use Chlp\OhMyPage\Model\Image;
use Chlp\OhMyPage\Router\Handler;
use DateTime;
use Exception;

class UploadImg extends Handler
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
        if (!isset($_FILES['image'])) {
            throw new Exception('no image to upload');
        }
    }

    public function run(): void
    {
        $page = App::getPageRepository()->getById($this->pageId);
        if ($page === null) {
            throw new Exception('page not found');
        }
        if (!isset($_POST['title'])) {
            throw new Exception('wrong request - title');
        }
        $image = new Image(
            Helper::genUuid(),
            new DateTime(),
            $_POST['title'],
            $width,
            $height,
            $format,
            $thumbnail,
            $hash
        );
        App::getImageRepository()->save($image);
        header('Location: /' . $page->id);
    }

    private function uploadImg(): array {
        // todo: upload dir определить
        $uploadFile = $uploadDir . basename($_FILES['image']['name']);
        if (!move_uploaded_file($_FILES['image']['tmp_name'], $uploadFile)) {
            throw new Exception('fail to upload image');
        }
        $imageInfo = getimagesize($uploadFile); // todo: сначала проверить изображение, размер, потом загружать
        $mimeType = mime_content_type($uploadFile);
        if ($imageInfo === false) {
            throw new Exception('wrong image');
        }

        echo "Тип изображения: " . $imageInfo['mime'] . "\n";
        echo "Ширина: " . $imageInfo[0] . " пикселей\n";
        echo "Высота: " . $imageInfo[1] . " пикселей\n";

        $thumbnailWidth = 200; // высчитать пропорции
        $thumbnailHeight = 200;
        $sourceImage = match ($mimeType) {
            'image/jpeg' => imagecreatefromjpeg($uploadFile),
            'image/png' => imagecreatefrompng($uploadFile),
            'image/gif' => imagecreatefromgif($uploadFile),
            default => throw new Exception('wrong image format'),
        };

        $thumb = imagecreatetruecolor($thumbnailWidth, $thumbnailHeight);
        imagecopyresampled($thumb, $sourceImage, 0, 0, 0, 0, $thumbnailWidth, $thumbnailHeight, $imageInfo[0], $imageInfo[1]);

        // todo: определить путь до временного хранилища
        $thumbnailFile = $uploadDir . 'thumb_' . basename($_FILES['image']['name']);
        switch ($mimeType) {
            case 'image/jpeg':
                imagejpeg($thumb, $thumbnailFile);
                break;
            case 'image/png':
                imagepng($thumb, $thumbnailFile);
                break;
            case 'image/gif':
                imagegif($thumb, $thumbnailFile);
                break;
        }

        imagedestroy($thumb);
        imagedestroy($sourceImage);
        // todo: вернуть данные по изображению
        return [];
    }
}