<?php
/**
 * Set Concurrent Live-Plus streams purchased to 15 for partners with FEATURE_EVENT_PLATFORM_PERMISSION enabled
 *
 *
 * Examples:
 * php enableEventPlatformPermissionToPartners.php
 * php enableEventPlatformPermissionToPartners.php realrun
 *
 * @package Deployment
 * @subpackage updates
 */

$dryRun = true;
if (in_array('realrun', $argv))
	$dryRun = false;

$countLimitEachLoop = 500;
$offset = $countLimitEachLoop;

const FEATURE_EVENT_PLATFORM_PERMISSION = 'FEATURE_EVENT_PLATFORM_PERMISSION';
const DEFAULT_MAX_OUTPUT_STREAMS = '15';

//------------------------------------------------------


require_once(__DIR__ . '/../../../deployment/bootstrap.php');

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
	foreach ($partners as $partner)
	{
		/* @var $partner Partner */
		$eventPlatformPermission = PermissionPeer::getByNameAndPartner(FEATURE_EVENT_PLATFORM_PERMISSION, $partner->getId());
		if ($eventPlatformPermission && $eventPlatformPermission->getStatus() == PermissionStatus::ACTIVE)
		{
			$partner->setMaxLiveStreamOutputs(DEFAULT_MAX_OUTPUT_STREAMS);
			print("Existing Event Platform permission on partner: [" . $partner->getId(). "] with status [" . $eventPlatformPermission->getStatus(). "] max output streams set to ". DEFAULT_MAX_OUTPUT_STREAMS . "\n");
			$partner->save();
		}
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

print("Done\n");
