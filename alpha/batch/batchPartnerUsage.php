#!/usr/bin/php
<?php
/*
 * Created on Apr 18, 2008
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
define('SF_ROOT_DIR',    realpath(__DIR__ . '/../'));
define('SF_APP',         'kaltura');
define('SF_ENVIRONMENT', 'batch');
define('SF_DEBUG',       true);

$sf_symfony_lib_dir = realpath(dirname(__FILE__).'/../../vendor/symfony');
$sf_symfony_data_dir = realpath(dirname(__FILE__).'/../../vendor/symfony-data');

$include_path = realpath(dirname(__FILE__).'/../../vendor/ZendFramework/library') . PATH_SEPARATOR . get_include_path();
set_include_path($include_path);

require_once($sf_symfony_lib_dir.'/util/sfCore.class.php');
sfCore::bootstrap($sf_symfony_lib_dir, $sf_symfony_data_dir);

require_once(SF_ROOT_DIR.DIRECTORY_SEPARATOR.'apps'.DIRECTORY_SEPARATOR.SF_APP.DIRECTORY_SEPARATOR.'lib/batch/myBatchPartnerUsage.class.php');

ini_set('memory_limit', '2048M');
kCurrentContext::$ps_vesion = 'ps2';
$batchClient = new myBatchPartnerUsage();

?>
