<?php

define('SF_ROOT_DIR',    realpath(dirname(__FILE__).'/../../../alpha/'));
define('SF_APP',         'kaltura');
define('SF_ENVIRONMENT', 'batch');
define('SF_DEBUG',       true);

require_once(SF_ROOT_DIR.DIRECTORY_SEPARATOR.'apps'.DIRECTORY_SEPARATOR.SF_APP.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'config.php');

$global_kaltura_memory_limit = "256M";
ini_set("memory_limit",$global_kaltura_memory_limit);

?>
