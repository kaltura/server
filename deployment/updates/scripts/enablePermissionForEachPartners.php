<?php
/**
 * enable feature for each partner
 * exmaples:
 * php enablePermissionForEachPartners.php FEATURE_ENTRY_REPLACEMENT 2 realrun 
 * php enablePermissionForEachPartners.php FEATURE_ENTRY_REPLACEMENT_APPROVAL 2 realrun
 *  
 * @package Deployment
 * @subpackage updates
 */

$dryRun = true; //TODO: change for real run
if (in_array ( 'realrun', $argv ))
	$dryRun = false;

if ($argc == 4){
	$permissionName = $argv [1];
	$permissionType = $argv [2];
} else {
	echo 'usage: php ' . $_SERVER ['SCRIPT_NAME'] . ' {permission_name} {permission_type} [realrun]' . PHP_EOL;
	die;
}
	
	
$stopFile = dirname ( __FILE__ ) . '/stop_flavor_migration'; // creating this file will stop the script
$countLimitEachLoop = 500;
$offset = $countLimitEachLoop;
//------------------------------------------------------


require_once (dirname ( __FILE__ ) . '/../../bootstrap.php');

$con = myDbHelper::getConnection ( myDbHelper::DB_HELPER_CONN_PROPEL2 );
KalturaStatement::setDryRun ( $dryRun );

$c = new Criteria ();
$c->add ( PartnerPeer::STATUS, Partner::PARTNER_STATUS_ACTIVE, Criteria::EQUAL );
$c->add ( PartnerPeer::ID, 99, Criteria::GREATER_THAN );
$c->setLimit ( $countLimitEachLoop );

$partners = PartnerPeer::doSelect ( $c, $con );

while ( count ( $partners ) ) {
	foreach ( $partners as $partner ) {
		/* @var $partner partner */
		echo 'set permission ' . $permissionName . ' for  partner id: ' . $partner->getId();
		$dbPermission = PermissionPeer::getByNameAndPartner ( $permissionName, $partner->getId () );
		var_dump($dbPermission);
		if (!$dbPermission) {
			
			$dbPermission = new Permission ();
			$dbPermission->setType ( $permissionType );
			$dbPermission->setPartnerId ( $partner->getId ());
			$dbPermission->setStatus ( PermissionStatus::ACTIVE );
			$dbPermission->setName($permissionName);
		}
		
		$dbPermission->setStatus ( PermissionStatus::ACTIVE );
		$dbPermission->save ();
		
	}

	$offset += $countLimitEachLoop;
	$c->setOffset($offset);
	$partners = PartnerPeer::doSelect ( $c, $con );
	sleep ( 1 );
}


