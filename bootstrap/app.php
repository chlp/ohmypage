<?php
declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/config.php';

use Chlp\Telepage\Application\App;
use Medoo\Medoo;

try {
    $database = new Medoo(DB_CONFIG);
} catch (Exception $ex) {
    echo '💾';
    error_log((string)$ex);
    exit;
}

$app = new App($database);

return $app;
