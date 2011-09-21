<?php
require_once ( dirname(__FILE__) . "/../../infra/kConf.php" ); // kaltura configuration
// symfony directories


$sf_symfony_lib_dir = realpath(dirname(__FILE__).'/../../symfony');
$sf_symfony_data_dir = realpath(dirname(__FILE__).'/../../symfony-data');

$include_path = realpath(dirname(__FILE__).'/../../vendor/ZendFramework/library') . PATH_SEPARATOR . get_include_path();
set_include_path($include_path);
