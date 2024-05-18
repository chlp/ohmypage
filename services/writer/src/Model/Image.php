<?php
declare(strict_types=1);

namespace Chlp\OhMyPage\Model;

use Chlp\OhMyPage\Application\Helper;
use Chlp\OhMyPage\Repository\ImageRepository;

class Image
{
    public const OhMyPageImgMdTag = 'OhMyPageImg';

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

    public static function replaceOhMyImgMdWithHtml(string $md): string
    {
        $pattern = '/!\[' . self::OhMyPageImgMdTag . '\]\(([a-z0-9]{' . Helper::ID_LENGTH . '})\)/';
        $callback = function ($matches) {
            $id = $matches[1];
            $image = (new ImageRepository())->getById($id);
            if ($image === null) {
                return $matches[0];
            }
            return $image->getHtmlImg();
        };
        return preg_replace_callback($pattern, $callback, $md);
    }

    public function getHtmlImg(): string
    {
        $html = '<img alt="' . $this->title . '" width="' . $this->width . '" height="' . $this->width . '" ';
        $html .= 'src="' . $this->thumbnail . '" full_src="' . $this->path . '" />';
        return $html;
    }
}