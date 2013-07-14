<?php
ini_set("memory_limit","256M");

require_once(__DIR__ . '/bootstrap.php');

if ( $argc == 3)
{	
	$assetParamsId = $argv[1];
	$permissionName = $argv[2];
}
else
{
	die ( 'usage: php ' . $_SERVER ['SCRIPT_NAME'] . " [asset_params_id] [permission_name]" . PHP_EOL );
}



$assetParams = assetParamsPeer::retrieveByPK($assetParamsId);
if (!$assetParams)
	die('Asset params id ' . $assetParamsId . ' not found');
	
$requiredPermissions = $assetParams->getRequiredPermissions();

if (is_null($requiredPermissions))
	$requiredPermissions = array();
	
$requiredPermissions[] = $permissionName;

$assetParams->setRequiredPermissions($requiredPermissions);
$assetParams->save();