<?php
declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/config.php';

use Chlp\OhMyPage\Application\App;
use MongoDB\Client as MongoDBClient;

try {
    $mongodbClient = new MongoDBClient(DB_CONFIG['URL']);
    $db = $mongodbClient->selectDatabase(DB_CONFIG['DB']);
} catch (Exception $ex) {
    echo '💾';
    error_log((string)$ex);
    exit;
}

$app = new App($db);

return $app;
