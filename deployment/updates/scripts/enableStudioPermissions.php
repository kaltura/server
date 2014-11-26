<?php
/**
 * Enable DRM_PLUGIN_PERMISSION to partners that already have WIDEVINE_PLUGIN_PERMISSION
 *
 *
 * Exmaples:
 * php enablePermissionForEachPartners.php
 * php enablePermissionForEachPartners.php realrun
 *
 * @package Deployment
 * @subpackage updates
 */

$dryRun = true;
if(in_array('realrun', $argv))
	$dryRun = false;

$countLimitEachLoop = 500;
$offset = $countLimitEachLoop;

$permissionNames = array ('FEATURE_SHOW_HTML_STUDIO','FEATURE_SHOW_FLASH_STUDIO');

//------------------------------------------------------


require_once (__DIR__ . '/../../bootstrap.php');

$con = myDbHelper::getConnection(myDbHelper::DB_HELPER_CONN_PROPEL2);
KalturaStatement::setDryRun($dryRun);

$c = new Criteria();
$c->addAscendingOrderByColumn(PartnerPeer::ID);
$c->addAnd(PartnerPeer::ID, 99, Criteria::GREATER_EQUAL);
$c->setLimit($countLimitEachLoop);

$partners = PartnerPeer::doSelect($c, $con);

while (count($partners))
{
	foreach($partners as $partner)
	{
		/* @var $partner Partner */
		foreach ($permissionNames as $permissionName)
		{
			KalturaLog::debug("Set permission [$permissionName] for partner id [". $partner->getId() ."]");
			$dbPermission = PermissionPeer::getByNameAndPartner($permissionName, $partner->getId());
			if(! $dbPermission)
			{		
				$dbPermission = new Permission();
				$dbPermission->setType(PermissionType::PLUGIN);
				$dbPermission->setPartnerId($partner->getId());
				$dbPermission->setName($permissionName);
			}
				
			$dbPermission->setStatus(PermissionStatus::ACTIVE);
			$dbPermission->save();
		}
	}
	
	kMemoryManager::clearMemory();
	$c = new Criteria();
	$c->addAscendingOrderByColumn(PartnerPeer::ID);
	$c->addAnd(PartnerPeer::ID, 99, Criteria::GREATER_EQUAL);
	$c->setLimit($countLimitEachLoop);
	$c->setOffset($offset);
	
	$partners = PartnerPeer::doSelect($c, $con);
	$offset +=  $countLimitEachLoop;
}

KalturaLog::debug("Done");
