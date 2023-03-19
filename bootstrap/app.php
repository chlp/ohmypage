<?php
declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/config.php';

use Chlp\Telepage\Application\App;
use Medoo\Medoo;

$database = new Medoo(DB_CONFIG);

$app = new App($database);

return $app;
