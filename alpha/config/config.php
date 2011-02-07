<?php
require_once ( "kConf.php" ); // kaltura configuration
// symfony directories

$sf_symfony_lib_dir = realpath(dirname(__FILE__).'/../../../symfony');
$sf_symfony_data_dir = realpath(dirname(__FILE__).'/../../../symfony-data');

$include_path = get_include_path() . PATH_SEPARATOR . realpath(dirname(__FILE__).'/../../vendor/ZendFramework/library');
set_include_path($include_path);
