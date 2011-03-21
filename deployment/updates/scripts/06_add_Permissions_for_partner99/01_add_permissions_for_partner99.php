<?php
/**
 * @package deployment
 * 
 * Adds default permissions to partner 99
 * 
 */

//-- Bootstraping
error_reporting(E_ALL);

require_once(dirname(__FILE__).'/../../../bootstrap.php');
require_once(ROOT_DIR . '/api_v3/bootstrap.php');

$dryRun = true; //TODO: change for real run
if($argc > 1 && $argv[1] == 'realrun')
	$dryRun = false;
	
define("TEMPLATE_PARTNER_ID", 99 );

//-- Script start
//Get partner 99
$partner99 = PartnerPeer::retrieveByPK(TEMPLATE_PARTNER_ID);

//Enable the vast
$partner99->setEnableVast(true);

//Enable plugin metadata
$partner99->setPluginEnabled('metadata', true);

if($dryRun)
{
	KalturaLog::log('DRY RUN - Adding new permissions [Vast, CustomMetadata, Thumbnails managment] to partner [99]\n');
	KalturaLog::log(var_dump($partner99));
}
else 
{
	KalturaLog::log('Adding new permissions [Vast, CustomMetadata, Thumbnails managment] to partner [99]');
	
	//save changes to DB
	$partner99->save();
}

KalturaLog::log('Done!');