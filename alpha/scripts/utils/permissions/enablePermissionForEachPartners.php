<?php
/**
 * enable feature for each partner
 * exmaples:
 * php enablePermissionForEachPartners.php FEATURE_ENTRY_REPLACEMENT 2 0 realrun
 * php enablePermissionForEachPartners.php FEATURE_ENTRY_REPLACEMENT_APPROVAL 2 1 realrun
 *
 * @package Deployment
 * @subpackage updates
 */

$dryRun = true; //TODO: change for real run
if (in_array ( 'realrun', $argv ))
	$dryRun = false;

if ($argc == 5){
	$permissionName = $argv [1];
	$permissionType = $argv [2];
	$includeTemplatePartners = $argv[3];
} else {
	echo 'usage: php ' . $_SERVER ['SCRIPT_NAME'] . ' {permission_name} {permission_type} {include_template_partners} [realrun]' . PHP_EOL;
	die;
}
	
$countLimitEachLoop = 500;
//------------------------------------------------------


require_once (dirname ( __FILE__ ) . '/../../bootstrap.php');

$con = myDbHelper::getConnection ( myDbHelper::DB_HELPER_CONN_PROPEL2 );
KalturaStatement::setDryRun ( $dryRun );

$lastPartnerId = 0;

while ( 1 ) {
	$c = new Criteria ();
	$c->add ( PartnerPeer::STATUS, Partner::PARTNER_STATUS_ACTIVE, Criteria::EQUAL );
	$c->add ( PartnerPeer::ID, $lastPartnerId, Criteria::GREATER_THAN );
	if(!$includeTemplatePartners)
		$c->add (PartnerPeer::PARTNER_GROUP_TYPE, PartnerGroupType::TEMPLATE, Criteria::NOT_EQUAL);
	$c->addAscendingOrderByColumn ( PartnerPeer::ID );
	$c->setLimit ( $countLimitEachLoop );
	$partners = PartnerPeer::doSelect ( $c, $con );
        if (!count($partners))
                break;

	foreach ( $partners as $partner ) {
		/* @var $partner partner */
		KalturaLog::debug("Set permission [$permissionName] for partner id [" . $partner->getId () . "]");
		$dbPermission = PermissionPeer::getByNameAndPartner ( $permissionName, $partner->getId () );
		var_dump($dbPermission);
		if (!$dbPermission) {
			
			$dbPermission = new Permission ();
			$dbPermission->setType ( $permissionType );
			$dbPermission->setPartnerId ( $partner->getId ());
			$dbPermission->setName($permissionName);
		}
		
		$dbPermission->setStatus ( PermissionStatus::ACTIVE );
		$dbPermission->save ();
                $lastPartnerId = $partner->getId();
	}

	kMemoryManager::clearMemory();
	sleep ( 1 );
}

KalturaLog::debug("Done");
