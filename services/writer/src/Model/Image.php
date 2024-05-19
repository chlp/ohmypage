<?php
declare(strict_types=1);

namespace Chlp\OhMyPage\Model;

use Chlp\OhMyPage\Application\Helper;
use Chlp\OhMyPage\Repository\ImageRepository;
use Chlp\OhMyPage\Application\App;

class Image
{
    public function __construct(
        public string $id,
        public string $title,
        public int $width,
        public int $height,
        public string $path,
        public string $format,
        public string $thumbnail,
    )
    {
    }

    public function getHtmlImg(): string
    {
        $html = '<img alt="' . $this->title . '" width="' . $this->width . '" height="' . $this->width . '" ';
        $html .= 'src="' . $this->thumbnail . '" full_src="' . $this->path . '" />';
        return $html;
    }
}