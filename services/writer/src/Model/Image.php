<?php
declare(strict_types=1);

namespace Chlp\OhMyPage\Model;

use Chlp\OhMyPage\Application\Helper;
use DateTime;

class Image
{
    public function __construct(
        public string $id,
        public DateTime $created,
        public string $title,
        public int $width,
        public int $height,
        public string $format,
        public string $thumbnail,
    )
    {
    }

    public function getHtmlImg(): string
    {
        $html = '<img class="ohmyimg" width="' . $this->width . '" height="' . $this->height . '" ';
        $html .= ' alt="' . $this->title . '" data-src="' . $this->getPath() . '" src="' . $this->thumbnail . '"/>';
        return $html;
    }

    private function getPath(): string
    {
        return SERVICES['images'] . 'upload/' .
            Helper::datetimeToOhMyPath($this->created) . '/' . $this->id . '.' . $this->format;
    }
}