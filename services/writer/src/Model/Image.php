<?php
declare(strict_types=1);

namespace Chlp\OhMyPage\Model;

use Chlp\OhMyPage\Application\Helper;
use DateTime;

class Image
{
    public const FORMAT_JPG = 'jpeg';
    public const FORMAT_PNG = 'png';
    public const FORMAT_GIF = 'gif';

    public function __construct(
        public readonly string $id,
        public readonly DateTime $created,
        public readonly int $width,
        public readonly int $height,
        public readonly string $format,
        public readonly string $thumbnail,
        public readonly string $hash,
    )
    {
    }

    public function getHtmlImg(string $title = ''): string
    {
        return '<img class="ohmyimg" width="' . $this->width . '" height="' . $this->height . '" '
            . ' alt="' . $title . '" data-src="' . $this->getHttpPath()
            . '" src="' . $this->getThumbnailImgSrc() . '"/>';
    }

    private function getThumbnailImgSrc(): string
    {
        return 'data: ' . $this->format . ';base64,' . $this->thumbnail;
    }

    private function getHttpPath(): string
    {
        return SERVICES['images'] .
            Helper::datetimeToOhMyPath($this->created) . '/' . $this->id . '.' . $this->format . '?' . $this->hash;
    }

    public function getFilePath(): string
    {
        return Helper::getFsVarDir() . '/upload_images/' . Helper::datetimeToOhMyPath($this->created) . "/" .
            $this->id . '.' . $this->format;
    }
}