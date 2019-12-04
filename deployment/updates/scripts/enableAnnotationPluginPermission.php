<?php
/**
 * Enable ANNOTATION_PLUGIN_PERMISSION to all partners
 *
 *
 * Exmaples:
 * php enableAnnotationPluginPermission.php
 * php enableAnnotationPluginPermission.php realrun
 *
 * @package Deployment
 * @subpackage updates
 */
 
$dryRun = true;
if(in_array('realrun', $argv))
	$dryRun = false;
$permissionName = 'ANNOTATION_PLUGIN_PERMISSION';
//------------------------------------------------------
require_once (__DIR__ . '/../../bootstrap.php');
$con = myDbHelper::getConnection(myDbHelper::DB_HELPER_CONN_PROPEL2);
KalturaStatement::setDryRun($dryRun);
$criteria = new Criteria();
$criteria->add(PermissionPeer::STATUS, PermissionStatus::ACTIVE);
$criteria->add(PermissionPeer::NAME, $permissionName);
$criteria->addAscendingOrderByColumn(PermissionPeer::PARTNER_ID);
$criteria->addSelectColumn(PermissionPeer::PARTNER_ID);
$stmt = PermissionPeer::doSelectStmt($criteria, $con);
$criteria = new Criteria(PartnerPeer::DATABASE_NAME);
$criteria->add(PartnerPeer::ID, $stmt->fetchAll(PDO::FETCH_COLUMN), Criteria::NOT_IN);
$partners = PartnerPeer::doSelect($criteria, $con);
KalturaLog::debug("found ". count($partners). " partners where ANNOTATION_PLUGIN_PERMISSION is not enabled");
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
