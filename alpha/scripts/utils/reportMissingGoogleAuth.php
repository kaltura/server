<?php
/**
 * This script lists all distribution profiles and check if they have a valid authentication info
 */
chdir('/opt/kaltura/Jupiter-10.9.0/alpha/scripts/utils');

require_once('/opt/kaltura/Jupiter-10.9.0/alpha/scripts/bootstrap.php');

if(count($argv) < 2)
{
	die ("CSV file name is required input.\n");
}

$filename = $argv[1];

$partnerId = null;
if(isset($argv[2]) && is_numeric($argv[2]))
{
	$partnerId = intval($argv[2]);
}

$csv = fopen($filename, 'w');

$distributionProvider = YoutubeApiDistributionPlugin::getDistributionProviderTypeCoreValue(YoutubeApiDistributionProviderType::YOUTUBE_API);

$criteria = new Criteria();
$criteria->add(DistributionProfilePeer::STATUS, DistributionProfileStatus::DELETED, Criteria::NOT_EQUAL);
$criteria->add(DistributionProfilePeer::PROVIDER_TYPE, $distributionProvider);

if($partnerId)
{
	$criteria->add(DistributionProfilePeer::PARTNER_ID, $partnerId);
}
else 
{
	$criteria->add(DistributionProfilePeer::CUSTOM_DATA, '%"demodistro"%', Criteria::NOT_LIKE);
	$criteria->addAscendingOrderByColumn(DistributionProfilePeer::PARTNER_ID);
}

$fields = array(
	'Partner ID',
	'Partner Name',
	'E-Mail',
	'Profile ID',
	'Profile Name',
	'Username',
	'Password',
	'Last Distribution Date',
	'Last Distribution Days Ago',
	'Authenticated',
	'URL',
);
fputcsv($csv, $fields);

$now = time();
$threeMonthsAgo = $now - (60 * 60 * 24 * 30 * 3);
$ks = null;
$distributionProfiles = DistributionProfilePeer::doSelect($criteria);
while($distributionProfiles){
	$lastId = 0;
	foreach($distributionProfiles as $distributionProfile)
	{
		/* @var $distributionProfile YoutubeApiDistributionProfile */
		
		$lastId = $distributionProfile->getId();
		$currentPartnerId = $distributionProfile->getPartnerId();
		$url = $distributionProfile->getApiAuthorizeUrl();
		$authenticated = 'No';
		if(is_null($url))
		{
			$authenticated = 'Yes';
			$url = '';
		}
		else
		{
			kSessionUtils::createKSessionNoValidations($currentPartnerId, null, $ks, 2592000, SessionType::ADMIN);
			$url .= "?ks=$ks";
		}
		
		$lastDistributionDate = 'Never';
		$lastDistributionDaysAgo = 'Never';
		
		$entryDistributionCriteria = new Criteria();
		$entryDistributionCriteria->add(EntryDistributionPeer::PARTNER_ID, $currentPartnerId);
		$entryDistributionCriteria->add(EntryDistributionPeer::STATUS, EntryDistributionStatus::READY);
		$entryDistributionCriteria->add(EntryDistributionPeer::DISTRIBUTION_PROFILE_ID, $lastId);
		$entryDistributionCriteria->addDescendingOrderByColumn(EntryDistributionPeer::CREATED_AT);
			
		$entryDistribution = EntryDistributionPeer::doSelectOne($entryDistributionCriteria);
		if($entryDistribution)
		{
			$lastDistributionDate = $entryDistribution->getCreatedAt();
			$lastDistributionDaysAgo = floor(($now - $entryDistribution->getCreatedAt(null)) / 86400);
		}
		
//		if(!$partnerId)
//		{
//			$entryDistributionCriteria = new Criteria();
//			$entryDistributionCriteria->add(EntryDistributionPeer::PARTNER_ID, $currentPartnerId);
//			$entryDistributionCriteria->add(EntryDistributionPeer::STATUS, EntryDistributionStatus::READY);
//			$entryDistributionCriteria->add(EntryDistributionPeer::DISTRIBUTION_PROFILE_ID, $lastId);
//			$entryDistributionCriteria->add(EntryDistributionPeer::CREATED_AT, $threeMonthsAgo, Criteria::GREATER_THAN);
//			
//			if(!EntryDistributionPeer::doSelectOne($entryDistributionCriteria))
//				continue;
//		}
		
		$partnerName = 'Unknown';
		$partnerEmail = 'Unknown';
		
		$partner = PartnerPeer::retrieveByPK($currentPartnerId);
		if($partner)
		{
			$partnerName = $partner->getPartnerName();
			$partnerEmail = $partner->getAdminEmail();
		}
		
		$fields = array(
			'partner_id' => $currentPartnerId,
			'partner_name' => $partnerName,
			'email' => $partnerEmail,
			'profile_id' => $lastId,
			'profile_name' => $distributionProfile->getName(),
			'username' => $distributionProfile->getUsername(),
			'password' => $distributionProfile->getPassword(),
			'last_distribution_date' => $lastDistributionDate,
			'last_distribution_days_ago' => $lastDistributionDaysAgo,
			'authenticated' => $authenticated,
			'url' => $url,
		);
		
		fputcsv($csv, $fields);
	}

	break;
//	$criteria->add(DistributionProfilePeer::ID, $lastId, Criteria::GREATER_THAN);
//	$distributionProfiles = DistributionProfilePeer::doSelect($criteria);
}

fclose($csv);
