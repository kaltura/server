<?php
/**
 * Enable FEATURE_KMC_AKAMAI_UNIVERSAL_LIVE_STREAM_PROVISION to partners that already have FEATURE_LIVE_STREAM
 * Arguments:
 *  - p - start from Partner id
 *  - u - start from Updated at (linux timestamp in seconds)
 *
 * Exmaples:
 * php enablePermissionForEachPartners.php
 * php enablePermissionForEachPartners.php realrun
 * php enablePermissionForEachPartners.php -p 99 realrun
 * php enablePermissionForEachPartners.php -u 1365292800 realrun
 *
 * @package Deployment
 * @subpackage updates
 */

$dryRun = true;
if(in_array('realrun', $argv))
	$dryRun = false;

$countLimitEachLoop = 500;
$offset = $countLimitEachLoop;
$permissionName = 'FEATURE_KMC_AKAMAI_UNIVERSAL_LIVE_STREAM_PROVISION';
$startPartnerId = null;
$startUpdatedAt = null;

$options = getopt('u:p:');

if(isset($options['p']))
{
	$startPartnerId = $options['p'];
	if(!is_numeric($startPartnerId))
	{
		echo "Only numeric arguments could be passed in partner id.";
		exit(-1);
	}
}

if(isset($options['u']))
{
	$startUpdatedAt = $options['u'];
	if(!is_numeric($startPartnerId))
	{
		echo "Only numeric arguments could be passed in updated at.";
		exit(-1);
	}
}

//------------------------------------------------------


require_once (__DIR__ . '/../../bootstrap.php');

$con = myDbHelper::getConnection(myDbHelper::DB_HELPER_CONN_PROPEL2);
KalturaStatement::setDryRun($dryRun);

$criteria = new Criteria();
$criteria->add(PermissionPeer::STATUS, PermissionStatus::ACTIVE);
$criteria->add(PermissionPeer::NAME, 'FEATURE_LIVE_STREAM');

if($startPartnerId)
	$criteria->add(PermissionPeer::PARTNER_ID, $startPartnerId, Criteria::GREATER_THAN);
	
if($startUpdatedAt)
	$criteria->add(PermissionPeer::UPDATED_AT, $startUpdatedAt, Criteria::GREATER_THAN);
	
$criteria->addAscendingOrderByColumn(PermissionPeer::PARTNER_ID);
$criteria->addSelectColumn(PermissionPeer::PARTNER_ID);
$criteria->setLimit($countLimitEachLoop);

$stmt = PermissionPeer::doSelectStmt($criteria, $con);
$partners = PartnerPeer::retrieveByPKs($stmt->fetchAll(PDO::FETCH_COLUMN));

while(count($partners))
{
	foreach($partners as $partner)
	{
		/* @var $partner partner */
		$partnerId = $partner->getId();
		KalturaLog::debug("Set permission [$permissionName] for partner id [$partnerId]");
		$dbPermission = PermissionPeer::getByNameAndPartner($permissionName, $partnerId);
		if(! $dbPermission)
		{
			
			$dbPermission = new Permission();
			$dbPermission->setType(PermissionType::SPECIAL_FEATURE);
			$dbPermission->setPartnerId($partnerId);
			$dbPermission->setName($permissionName);
		}
		
		$dbPermission->setStatus(PermissionStatus::ACTIVE);
		$dbPermission->save();
	}
	
	kMemoryManager::clearMemory();
	$criteria->setOffset($offset);
	$stmt = PermissionPeer::doSelectStmt($criteria, $con);
	$partners = PartnerPeer::retrieveByPKs($stmt->fetchAll(PDO::FETCH_COLUMN));
	usleep(100);
	$offset += $countLimitEachLoop;
}

KalturaLog::debug("Done");
