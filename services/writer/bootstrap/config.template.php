<?php
declare(strict_types=1);

const DB_CONFIG = [
    'URL' => 'mongodb+srv://db_reminder_user:<password>@cluster.*.mongodb.net/',
    'DB' => 'ohmy'
];

const SERVICES = [
    'writer' => 'http://localhost:8130/',
    'reader' => 'http://localhost:8131/',
    'images' => 'http://localhost:8132/',
    'videos' => 'http://localhost:8133/',
    'files' => 'http://localhost:8134/',
];