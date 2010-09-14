#!/usr/bin/php
<?php

require_once(realpath(dirname(__FILE__)).'/../config/sfrootdir.php');
define('SF_APP',         'kaltura');
define('SF_ENVIRONMENT', 'batch');
define('SF_DEBUG',       true);

require_once(SF_ROOT_DIR.DIRECTORY_SEPARATOR.'apps'.DIRECTORY_SEPARATOR.SF_APP.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'config.php');
require_once(SF_ROOT_DIR.DIRECTORY_SEPARATOR.'apps'.DIRECTORY_SEPARATOR.SF_APP.DIRECTORY_SEPARATOR.'lib/batch/myBatchNotificationServer.class.php');

$script_name = $_SERVER['SCRIPT_NAME'];
$batchNotificationServer = new myBatchNotificationServer( $script_name );
$batchNotificationServer->doNotifyLoop();

?>
