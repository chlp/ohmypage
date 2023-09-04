<?php
declare(strict_types=1);

use Chlp\Telepage\Application\App;

/** @var App $app */
$app = require __DIR__ . '/../bootstrap/app.php';
$app->run();
