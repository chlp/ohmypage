<?php
declare(strict_types=1);

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/../vendor/autoload.php';

use Chlp\OhMyPage\Application\App;

try {
    return new App();
} catch (Exception $ex) {
    echo '💾';
    error_log((string)$ex);
    exit;
}
