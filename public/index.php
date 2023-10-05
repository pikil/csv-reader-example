<?php

declare(strict_types=1);

namespace Pikil\CsvReader;

const WWW_PATH = __DIR__;

require WWW_PATH . '/../api/vendor/autoload.php';

$router = new \Pikil\CsvReader\Routing\Router();
$router->handleRequest();
