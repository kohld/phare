<?php

declare(strict_types=1);

require_once __DIR__ . '/../../vendor/autoload.php';

use Phare\ConfigLoader;
use Phare\Generator;

$baseDir = __DIR__;
$config = new ConfigLoader($baseDir . '/config.yml');
$generator = new Generator($config, $baseDir);
$generator->generate();
