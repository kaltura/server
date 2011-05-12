<?php 
/**
 * @package infra
 * @subpackage autoloader
 */
define("KALTURA_ROOT_PATH", realpath(dirname(__FILE__).DIRECTORY_SEPARATOR."../"));
define("KALTURA_INFRA_PATH", KALTURA_ROOT_PATH.DIRECTORY_SEPARATOR."infra");

define("KALTURA_API_V3", true); // used for different logic in alpha libs

if (!defined("SF_ROOT_DIR"))    // when bootstraping api v3 under symfony, this will throw notices
{
	define("SF_ROOT_DIR",   KALTURA_ROOT_PATH.DIRECTORY_SEPARATOR."alpha");
	define("SF_APP",         'kaltura'); 
	define("SF_ENVIRONMENT", 'prod');
	define("SF_DEBUG",       false);
}