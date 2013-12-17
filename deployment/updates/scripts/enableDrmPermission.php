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


$permissionName = 'DRM_PLUGIN_PERMISSION';

//------------------------------------------------------


require_once (__DIR__ . '/../../bootstrap.php');

$con = myDbHelper::getConnection(myDbHelper::DB_HELPER_CONN_PROPEL2);
KalturaStatement::setDryRun($dryRun);

$criteria = new Criteria();
$criteria->add(PermissionPeer::STATUS, PermissionStatus::ACTIVE);
$criteria->add(PermissionPeer::NAME, 'WIDEVINE_PLUGIN_PERMISSION');
	
$criteria->addAscendingOrderByColumn(PermissionPeer::PARTNER_ID);
$criteria->addSelectColumn(PermissionPeer::PARTNER_ID);

$stmt = PermissionPeer::doSelectStmt($criteria, $con);
$partners = PartnerPeer::retrieveByPKs($stmt->fetchAll(PDO::FETCH_COLUMN));

KalturaLog::debug("found ". count($partners). " partners");
foreach($partners as $partner)
{
	/* @var $partner partner */
	$partnerId = $partner->getId();
	KalturaLog::debug("Set permission [$permissionName] for partner id [$partnerId]");
	$dbPermission = PermissionPeer::getByNameAndPartner($permissionName, $partnerId);
	if(! $dbPermission)
	{		
		$dbPermission = new Permission();
		$dbPermission->setType(PermissionType::PLUGIN);
		$dbPermission->setPartnerId($partnerId);
		$dbPermission->setName($permissionName);
	}
		
	$dbPermission->setStatus(PermissionStatus::ACTIVE);
	$dbPermission->save();
}
	
kMemoryManager::clearMemory();

KalturaLog::debug("Done");
