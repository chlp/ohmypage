<?php
declare(strict_types=1);

namespace Chlp\OhMyPage\Router\Handlers;

use Chlp\OhMyPage\Application\App;
use Chlp\OhMyPage\Application\Helper;
use Chlp\OhMyPage\Model\Image;
use Chlp\OhMyPage\Router\Handler;
use DateTime;
use Exception;
use function base64_encode;
use function file_get_contents;
use function filesize;
use function hash_file;
use function imagecopyresampled;
use function imagecreatefromjpeg;
use function imagecreatetruecolor;
use function imagedestroy;
use function imagegif;
use function imagejpeg;
use function imagepng;
use function is_string;
use function mime_content_type;
use function move_uploaded_file;
use function sys_get_temp_dir;
use function tempnam;

class UploadImg extends Handler
{
    private const MAX_FILE_SIZE = 30 * 1024 * 1024; // 30 MB

    private const MAX_THUMBNAIL_SIDE_SIZE = 200;

    private const MIME_JPG = 'image/jpeg';
    private const MIME_PNG = 'image/png';
    private const MIME_GIF = 'image/gif';

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
        $image = $this->makeImgFromUpload();
        App::getImageRepository()->save($image);
        header('Location: /' . $page->id);
    }

    /**
     * @throws Exception
     */
    private function makeImgFromUpload(): Image
    {
        $id = Helper::genUuid();

        $uploadedImgPath = $_FILES['image']['tmp_name'];
        if (filesize($uploadedImgPath) > self::MAX_FILE_SIZE) {
            throw new Exception('file size exceeds the maximum');
        }

        $hash = hash_file('sha256', $uploadedImgPath);
        if (!is_string($hash)) {
            throw new Exception('problem with calculating hash');
        }

        $mimeType = mime_content_type($uploadedImgPath);
        $sourceImage = match ($mimeType) {
            self::MIME_JPG => imagecreatefromjpeg($uploadedImgPath),
            self::MIME_PNG => imagecreatefrompng($uploadedImgPath),
            self::MIME_GIF => imagecreatefromgif($uploadedImgPath),
            default => throw new Exception('wrong image mime type'),
        };
        $imageInfo = getimagesize($uploadedImgPath);
        if ($imageInfo === false) {
            throw new Exception('wrong image');
        }

        switch ($imageInfo[2]) {
            case IMAGETYPE_JPEG:
                if ($mimeType !== self::MIME_JPG) {
                    throw new Exception('wrong image type 1');
                }
                break;
            case IMAGETYPE_PNG:
                if ($mimeType !== self::MIME_PNG) {
                    throw new Exception('wrong image type 2');
                }
                break;
            case IMAGETYPE_GIF:
                if ($mimeType !== self::MIME_GIF) {
                    throw new Exception('wrong image type 3');
                }
                break;
        }

        $width = $imageInfo[0];
        $height = $imageInfo[1];
        if ($width >= $height) {
            $thumbnailWidth = self::MAX_THUMBNAIL_SIDE_SIZE;
            $thumbnailHeight = (int)(self::MAX_THUMBNAIL_SIDE_SIZE * $height / $width);
        } else {
            $thumbnailHeight = self::MAX_THUMBNAIL_SIDE_SIZE;
            $thumbnailWidth = (int)(self::MAX_THUMBNAIL_SIDE_SIZE * $width / $height);
        }

        $thumbnailImage = imagecreatetruecolor($thumbnailWidth, $thumbnailHeight);
        if ($thumbnailImage === false) {
            throw new Exception('error in creating thumbnail image');
        }
        imagecopyresampled($thumbnailImage, $sourceImage, 0, 0, 0, 0, $thumbnailWidth, $thumbnailHeight, $width, $height);

        $thumbnailPath = tempnam(sys_get_temp_dir(), $id);
        $success = match ($mimeType) {
            self::MIME_JPG => imagejpeg($thumbnailImage, $thumbnailPath),
            self::MIME_PNG => imagepng($thumbnailImage, $thumbnailPath),
            self::MIME_GIF => imagegif($thumbnailImage, $thumbnailPath),
            default => throw new Exception('wrong image mime type 2'),
        };
        if (!$success) {
            throw new Exception('error in saving thumbnail image');
        }

        $thumbnailBase64 = base64_encode(file_get_contents($thumbnailPath));

        imagedestroy($thumbnailImage);
        imagedestroy($sourceImage);

        $image = new Image(
            $id,
            new DateTime(),
            $width,
            $height,
            self::mimeToFormat($mimeType),
            $thumbnailBase64,
            $hash
        );

        $filePath = $image->getFilePath();
        if (!is_dir(dirname($filePath))) {
            mkdir(dirname($filePath), 0777, true);
        }
        if (!move_uploaded_file($uploadedImgPath, $filePath)) {
            throw new Exception('fail to upload image');
        }

        return $image;
    }

    private static function mimeToFormat(string $mime): string
    {
        return match ($mime) {
            self::MIME_JPG => Image::FORMAT_JPG,
            self::MIME_PNG => Image::FORMAT_PNG,
            self::MIME_GIF => Image::FORMAT_GIF,
            default => throw new Exception('wrong mime'),
        };
    }
}