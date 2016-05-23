<?php
chdir(__DIR__);

// Autoloader
require_once(__DIR__ . "/KAutoloader.php");
KAutoloader::addClassPath(KAutoloader::buildPath(__DIR__, '*'));
KAutoloader::setClassMapFilePath(__DIR__ . '/cache/classMap.cache');
KAutoloader::register();

// Timezone
date_default_timezone_set('America/New_York');
