<?php
/**
 * Enable FEATURE_V3_STUDIO_PERMISSION to partners that dont have it
 *
 *
 * Examples:
 * php enableStudioV3Permission.php
 * php enableStudioV3Permission.php realrun
 *
 * @package Deployment
 * @subpackage updates
 */

$dryRun = true;
if(in_array('realrun', $argv))
	$dryRun = false;

$countLimitEachLoop = 500;
$offset = $countLimitEachLoop;

const FEATURE_V3_STUDIO_PERMISSION = 'FEATURE_V3_STUDIO_PERMISSION';

//------------------------------------------------------


require_once (__DIR__ . '/../../bootstrap.php');

$con = myDbHelper::getConnection(myDbHelper::DB_HELPER_CONN_PROPEL2);
KalturaStatement::setDryRun($dryRun);

$c = new Criteria();
$c->addAscendingOrderByColumn(PartnerPeer::ID);
$c->addAnd(PartnerPeer::ID, 99, Criteria::GREATER_EQUAL);
$c->addAnd(PartnerPeer::STATUS,1, Criteria::EQUAL);
$c->setLimit($countLimitEachLoop);

$partners = PartnerPeer::doSelect($c, $con);

while (count($partners))
{
	foreach($partners as $partner)
	{
		/* @var $partner Partner */
		KalturaLog::debug("Set permission [" . FEATURE_V3_STUDIO_PERMISSION . "] for partner id [". $partner->getId() ."]");
		$dbPermission = PermissionPeer::getByNameAndPartner(FEATURE_V3_STUDIO_PERMISSION, $partner->getId());
		if(! $dbPermission)
		{
			$dbPermission = new Permission();
			$dbPermission->setType(PermissionType::PLUGIN);
			$dbPermission->setPartnerId($partner->getId());
			$dbPermission->setName(FEATURE_V3_STUDIO_PERMISSION);
		}
		
		$dbPermission->setStatus(PermissionStatus::ACTIVE);
		$dbPermission->save();
	}
	
	kMemoryManager::clearMemory();
	$c = new Criteria();
	$c->addAscendingOrderByColumn(PartnerPeer::ID);
	$c->addAnd(PartnerPeer::ID, 99, Criteria::GREATER_EQUAL);
	$c->addAnd(PartnerPeer::STATUS,1, Criteria::EQUAL);
	$c->setLimit($countLimitEachLoop);
	$c->setOffset($offset);
	
	$partners = PartnerPeer::doSelect($c, $con);
	$offset +=  $countLimitEachLoop;
}

KalturaLog::debug("Done");
