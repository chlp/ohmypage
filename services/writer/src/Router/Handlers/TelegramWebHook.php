<?php
declare(strict_types=1);

namespace Chlp\OhMyPage\Router\Handlers;

use Chlp\OhMyPage\Application\Helper;
use Chlp\OhMyPage\Router\Handler;

class TelegramWebHook extends Handler
{
    public function run(): void
    {
        $data = $this->getJson();
        if (!isset($data['message']['from']['id']) || !is_int($data['message']['from']['id']) ||
            !isset($data['message']['text']) || !is_string($data['message']['text'])) {
            Helper::log("raw telegram");
            $this->setHtml('wrong telegram message');
            parent::run();
            return;
        }
        $telegramUserId = (int)$data['message']['from']['id'];
        $text = (string)$data['message']['text'];

        switch ($text) {
            case 'auth':
                Helper::log("auth telegram");
                if ($telegramUserId === 158313752) { // todo: use db
                    // delete "auth" message
                    // send telegram message with link
                }
                $this->setHtml('auth telegram message');
                break;
            default:
                Helper::log("wrong action telegram");
                $this->setHtml('wrong action telegram message');
        }
    }
}