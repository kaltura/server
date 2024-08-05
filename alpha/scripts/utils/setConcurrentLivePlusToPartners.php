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
			updateMaxLiveStreamOutputs($partner, $eventPlatformPermission->getStatus());

			addLiveFlavorsToLiveTranscodingProfile($partner->getId(), $con);
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

function updateMaxLiveStreamOutputs($partner, $eventPlatformPermissionStatus): void
{
	$partner->setMaxLiveStreamOutputs(DEFAULT_MAX_OUTPUT_STREAMS);
	print("Existing Event Platform permission on partner: [" . $partner->getId(). "] with status [" . $eventPlatformPermissionStatus. "] max output streams set to ". DEFAULT_MAX_OUTPUT_STREAMS . "\n");
	$partner->save();
}

function addLiveFlavorsToLiveTranscodingProfile($partnerId, $con)
{
		$conversionProfileCriteria = new Criteria();
		$conversionProfileCriteria->add(conversionProfile2Peer::PARTNER_ID, $partnerId, Criteria::EQUAL);
		$conversionProfileCriteria->add(conversionProfile2Peer::SYSTEM_NAME, 'Default_Live', Criteria::EQUAL);
		$conversionProfile = conversionProfile2Peer::doSelectOne($conversionProfileCriteria, $con);

		if ($conversionProfile)
		{
			$flavorParamsConversionProfileCriteria = new Criteria();
			$flavorParamsConversionProfileCriteria->add(flavorParamsConversionProfilePeer::CONVERSION_PROFILE_ID, $conversionProfile->getId(), Criteria::EQUAL);
			$flavorParamsConversionProfileCriteria->add(flavorParamsConversionProfilePeer::FLAVOR_PARAMS_ID, 42, Criteria::EQUAL);
			$flavorParamsConversionProfile = flavorParamsConversionProfilePeer::doSelectOne($flavorParamsConversionProfileCriteria, $con);

			if(!$flavorParamsConversionProfile)
			{
				$flavorParamsConversionProfile42 = new flavorParamsConversionProfile;
				/** @var $flavorParamsConversionProfile $flavorParamsConversionProfile42 */
				$flavorParamsConversionProfile42->setConversionProfileId($conversionProfile->getId());
				$flavorParamsConversionProfile42->setFlavorParamsId(42);
				$flavorParamsConversionProfile42->setSystemName('HD/720 - WEB/MBL (H264/1700)');
				$flavorParamsConversionProfile42->save();
				print("flavor 42 added to Conversion Profile 'Cloud transcode' for partner [" . $partnerId . "]\n");
			}

			$flavorParamsConversionProfileCriteria->add(flavorParamsConversionProfilePeer::FLAVOR_PARAMS_ID, 43, Criteria::EQUAL);
			$flavorParamsConversionProfile = flavorParamsConversionProfilePeer::doSelectOne($flavorParamsConversionProfileCriteria, $con);
			if(!$flavorParamsConversionProfile)
			{
				$flavorParamsConversionProfile43 = new flavorParamsConversionProfile;
				/** @var $flavorParamsConversionProfile $flavorParamsConversionProfile43 */
				$flavorParamsConversionProfile43->setConversionProfileId($conversionProfile->getId());
				$flavorParamsConversionProfile43->setFlavorParamsId(43);
				$flavorParamsConversionProfile43->setSystemName('HD/720 - WEB/MBL (H264/2600)');
				$flavorParamsConversionProfile43->save();
			}
		}
		else
		{
			print("Conversion Profile 'Cloud transcode' for partner [" . $partnerId . "] was not found\n");
		}
}

print("Done\n");
