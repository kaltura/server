<?php

chdir(dirname(__FILE__));
require_once(dirname(__FILE__) . '/../bootstrap.php');

$dc_config = kConf::getMap("dc_config");
var_dump($dc_config);