<?php

if($argc < 3)
{
	echo "Usage:\n";
	echo "	php " . __FILE__ . " {conversion profile type - media (1) or live (2)} {comma seperated asset params ids (no spaces)}\n";
	exit(-1);
}

$conversionProfileType = $argv[1];
$additioalFlavorParamsIds = explode(',', $argv[2]);

chdir(__DIR__);
require_once (__DIR__ . '/../bootstrap.php');

$additioalFlavorParamsItems = assetParamsPeer::retrieveByPKs($additioalFlavorParamsIds);
if(count($additioalFlavorParamsItems) != count($additioalFlavorParamsIds))
{
	echo "Not all asset params found\n";
	echo "Usage:\n";
	echo "	php " . __FILE__ . " {conversion profile type - media (1) or live (2)} {comma seperated asset params ids (no spaces)}\n";
	exit(-1);
}

$additioalFlavorParamsObjects = array();
foreach($additioalFlavorParamsItems as $additioalFlavorParamsItem)
{
	/* @var $additioalFlavorParamsItem liveParams */
	$additioalFlavorParamsObjects[$additioalFlavorParamsItem->getId()] = $additioalFlavorParamsItem;
}

$partnerCriteria = new Criteria();
$partnerCriteria->add(PartnerPeer::PARTNER_GROUP_TYPE, PartnerGroupType::TEMPLATE);
$partnerCriteria->add(PartnerPeer::PARTNER_PARENT_ID, 0);
$partnerCriteria->add(PartnerPeer::STATUS, Partner::PARTNER_STATUS_ACTIVE);

$partners = PartnerPeer::doSelect($partnerCriteria);
foreach($partners as $partner)
{
	/* @var $partner Partner */
	
	$profileCriteria = new Criteria();
	$profileCriteria->add(conversionProfile2Peer::PARTNER_ID, $partner->getId());
	$profileCriteria->add(conversionProfile2Peer::TYPE, $conversionProfileType);
	$profileCriteria->add(conversionProfile2Peer::STATUS, ConversionProfileStatus::DELETED, Criteria::NOT_EQUAL);
	
	$profiles = conversionProfile2Peer::doSelect($profileCriteria);
	foreach($profiles as $profile)
	{
		/* @var $profile conversionProfile2 */
		
		$flavorParamsConversionProfileIds = flavorParamsConversionProfilePeer::getFlavorIdsByProfileId($profile->getId());
		foreach($additioalFlavorParamsObjects as $additioalFlavorParamsId => $additioalFlavorParamsObject)
		{
			if(in_array($additioalFlavorParamsId, $flavorParamsConversionProfileIds))
				continue;
			
			$flavorParamsConversionProfile = new flavorParamsConversionProfile();
			$flavorParamsConversionProfile->setConversionProfileId($profile->getId());
			$flavorParamsConversionProfile->setFlavorParamsId($additioalFlavorParamsId);
			$flavorParamsConversionProfile->setReadyBehavior($additioalFlavorParamsObject->getReadyBehavior());
			$flavorParamsConversionProfile->setSystemName($additioalFlavorParamsObject->getSystemName());
			$flavorParamsConversionProfile->setForceNoneComplied(false);
			
			if($additioalFlavorParamsObject->hasTag(assetParams::TAG_SOURCE) || $additioalFlavorParamsObject->hasTag(assetParams::TAG_INGEST))
				$flavorParamsConversionProfile->setOrigin(assetParamsOrigin::INGEST);
			else
				$flavorParamsConversionProfile->setOrigin(assetParamsOrigin::CONVERT);
			
			$flavorParamsConversionProfile->save();
		}
	}
}

exit(0);