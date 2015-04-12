<?php
/**
 * This script lists all distribution profiles and check if they have a valid authentication info
 */


require_once(__DIR__.'/../bootstrap.php');

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

$criteria->addAscendingOrderByColumn(DistributionProfilePeer::ID);
$criteria->setLimit(100);

$fields = array(
	'ID',
	'URL',
	'Username',
	'Password',
);
fputcsv($csv, $fields);

$ks = null;
$distributionProfiles = DistributionProfilePeer::doSelect($criteria);
while($distributionProfiles){
	$lastId = 0;
	foreach($distributionProfiles as $distributionProfile)
	{
		/* @var $distributionProfile YoutubeApiDistributionProfile */
		
		$lastId = $distributionProfile->getId();
		$url = $distributionProfile->getApiAuthorizeUrl();
		if(is_null($url))
			continue;
			
		$partnerId = $distributionProfile->getPartnerId();
		kSessionUtils::createKSessionNoValidations($partnerId, null, $ks, 86400, SessionType::ADMIN);
		
		$fields = array(
			$lastId,
			"$url?ks=$ks",
			$distributionProfile->getUsername(),
			$distributionProfile->getPassword(),
		);
		fputcsv($csv, $fields);
	}
	
	$criteria->add(DistributionProfilePeer::ID, $lastId, Criteria::GREATER_THAN);
	$distributionProfiles = DistributionProfilePeer::doSelect($criteria);
}

fclose($csv);
