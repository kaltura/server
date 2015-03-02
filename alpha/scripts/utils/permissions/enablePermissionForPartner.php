<?php
/**
 * enable feature for specific partner
 *
 * @package Deployment
 * @subpackage updates
 */

$dryRun = true; //TODO: change for real run
if (in_array ( 'realrun', $argv ))
	$dryRun = false;

if ($argc > 3){
	$partnerId = $argv [1];
	$permissionName = $argv [2];
	$permissionType = $argv [3];
} else {
	echo 'usage: php ' . $_SERVER ['SCRIPT_NAME'] . ' {partner_id} {permission_name} {permission_type} [realrun]' . PHP_EOL;
	die;
}
	
//------------------------------------------------------

require_once (dirname ( __FILE__ ) . '/../../bootstrap.php');

$con = myDbHelper::getConnection ( myDbHelper::DB_HELPER_CONN_PROPEL2 );
KalturaStatement::setDryRun ( $dryRun );

$dbPermission = PermissionPeer::getByNameAndPartner ( $permissionName, $partnerId );
var_dump($dbPermission);
if (!$dbPermission) {
	
	$dbPermission = new Permission ();
	$dbPermission->setType ( $permissionType );
	$dbPermission->setPartnerId ( $partnerId);
	$dbPermission->setName($permissionName);
}
		
$dbPermission->setStatus ( PermissionStatus::ACTIVE );
$dbPermission->save ();

kMemoryManager::clearMemory();

KalturaLog::debug("Done");
